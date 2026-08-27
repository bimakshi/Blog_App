<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Only logged-in users can edit stories
require_login();

// Get blog ID from URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Check if a valid ID was provided
if (!$id) {
    set_flash('error', 'Invalid story.');
    redirect('myblogs.php');
}

// Get the blog and make sure it belongs to the logged-in user
$stmt = $pdo->prepare(
    "SELECT *
     FROM blogs
     WHERE id = ?
     AND user_id = ?"
);

$stmt->execute([
    $id,
    current_user_id()
]);

$blog = $stmt->fetch();

// Blog doesn't exist or doesn't belong to this user
if (!$blog) {
    set_flash(
        'error',
        'Story not found or you do not have permission to edit it.'
    );

    redirect('myblogs.php');
}


// Get categories
$category_stmt = $pdo->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

$categories = $category_stmt->fetchAll();


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_csrf();

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    $category_id = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    $new_image_name = $blog['image'] ?? null;
    $old_image_name = $blog['image'] ?? null;

    // Validate title, content and category
    if ($title === '' || $content === '') {

        set_flash(
            'error',
            'Please enter both a story title and content.'
        );

    } elseif (!$category_id) {

        set_flash(
            'error',
            'Please select a category.'
        );

    } else {

        // Check whether a new image was uploaded
        $has_new_image =
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;


        if ($has_new_image) {

            $image = $_FILES['image'];

            // Check upload error
            if ($image['error'] !== UPLOAD_ERR_OK) {

                set_flash(
                    'error',
                    'There was a problem uploading your image.'
                );

            } else {

                // Maximum size: 5 MB
                $max_size = 5 * 1024 * 1024;

                // Allowed extensions
                $allowed_extensions = [
                    'jpg',
                    'jpeg',
                    'png',
                    'webp'
                ];

                // Get extension
                $extension = strtolower(
                    pathinfo(
                        $image['name'],
                        PATHINFO_EXTENSION
                    )
                );

                // Validate size
                if ($image['size'] > $max_size) {

                    set_flash(
                        'error',
                        'Image size must be 5 MB or smaller.'
                    );

                // Validate extension
                } elseif (
                    !in_array(
                        $extension,
                        $allowed_extensions,
                        true
                    )
                ) {

                    set_flash(
                        'error',
                        'Only JPG, JPEG, PNG, and WebP images are allowed.'
                    );

                } else {

                    // Verify actual image
                    $image_info = getimagesize(
                        $image['tmp_name']
                    );

                    if ($image_info === false) {

                        set_flash(
                            'error',
                            'Please upload a valid image file.'
                        );

                    } else {

                        // Generate unique filename
                        $new_image_name =
                            bin2hex(random_bytes(16))
                            . '.'
                            . $extension;

                        $upload_directory =
                            __DIR__ . '/uploads/blogs/';

                        if (!is_dir($upload_directory)) {

                            mkdir(
                                $upload_directory,
                                0755,
                                true
                            );
                        }

                        $upload_path =
                            $upload_directory . $new_image_name;


                        if (!move_uploaded_file(
                            $image['tmp_name'],
                            $upload_path
                        )) {

                            set_flash(
                                'error',
                                'Unable to save the uploaded image.'
                            );

                            $new_image_name = $old_image_name;
                        }
                    }
                }
            }
        }


        // Update blog if validation succeeded
        if (
            $title !== '' &&
            $content !== '' &&
            $category_id &&
            (!$has_new_image || $new_image_name !== $old_image_name || empty($old_image_name))
        ) {

            $stmt = $pdo->prepare(
                "UPDATE blogs
                 SET title = ?,
                     content = ?,
                     category_id = ?,
                     image = ?,
                     updated_at = NOW()
                 WHERE id = ?
                 AND user_id = ?"
            );

            $stmt->execute([
                $title,
                $content,
                $category_id,
                $new_image_name,
                $id,
                current_user_id()
            ]);


            // Delete old image after successful replacement
            if (
                $has_new_image &&
                !empty($old_image_name) &&
                $new_image_name !== $old_image_name
            ) {

                $old_image_path =
                    __DIR__ . '/uploads/blogs/' . $old_image_name;

                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }


            set_flash(
                'success',
                'Your story has been updated.'
            );

            redirect('myblogs.php');
        }
    }


    // Keep entered values if validation fails
    $blog['title'] = $title;
    $blog['content'] = $content;
    $blog['category_id'] = $category_id;
}


// Get flash message
$flash = get_flash();

require_once __DIR__ . '/includes/header.php';
?>

<section class="write-page">

    <div class="write-header">

        <span class="write-label">
            EDIT YOUR STORY
        </span>

        <h2>
            Edit Story
        </h2>

        <p>
            Update your story and keep sharing your ideas with the BlogNest community.
        </p>

    </div>


    <div class="write-card">

        <form
            method="post"
            class="write-form"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= csrf_token() ?>"
            >


            <!-- Story Title -->

            <div class="form-group">

                <label for="title">
                    Story Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Give your story a title..."
                    value="<?= sanitize($blog['title']) ?>"
                    required
                >

            </div>


            <!-- Category -->

            <div class="form-group">

                <label for="category_id">
                    Category
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                >

                    <option value="">
                        Select a category
                    </option>

                    <?php foreach ($categories as $category): ?>

                        <option
                            value="<?= $category['id'] ?>"
                            <?= (
                                $blog['category_id'] == $category['id']
                            ) ? 'selected' : '' ?>
                        >
                            <?= sanitize($category['name']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Current Image -->

            <?php if (!empty($blog['image'])): ?>

                <div class="form-group">

                    <label>
                        Current Cover Image
                    </label>

                    <div class="edit-current-image">

                        <img
                            src="uploads/blogs/<?= sanitize($blog['image']) ?>"
                            alt="<?= sanitize($blog['title']) ?>"
                        >

                    </div>

                </div>

            <?php endif; ?>


            <!-- New Image -->

<div class="form-group">

    <label for="image">
        Change Cover Image
    </label>

    <label
        for="image"
        class="image-upload-box"
        id="imageUploadBox"
    >

        <span class="image-upload-icon">
            ↑
        </span>

        <span class="image-upload-title">
            Choose a new cover image
        </span><br>

        <span class="image-upload-text">
            Leave empty to keep the current image
        </span><br>

        <span class="image-upload-size">
            JPG, JPEG, PNG or WebP · Maximum 5 MB
        </span>

    </label>

    <input
        type="file"
        id="image"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
        class="image-file-input"
    >

    <!-- Selected Image Preview -->
    <div
        id="imagePreviewContainer"
        class="edit-current-image"
        style="display: none; margin-top: 15px;"
    >
        <img
            id="imagePreview"
            src=""
            alt="Selected cover image"
        >
    </div>

    <small id="selectedImageName"></small>

</div>

            <!-- Story Content -->

            <div class="form-group">

                <label for="content">
                    Your Story
                </label>

                <textarea
                    id="content"
                    name="content"
                    rows="14"
                    placeholder="Start writing your story..."
                    required
                ><?= sanitize($blog['content']) ?></textarea>

            </div>


            <!-- Actions -->

            <div class="write-actions">

                <span class="draft-note">
                    Your changes will be saved immediately.
                </span>

                <button
                    type="submit"
                    class="publish-btn"
                >
                    Update Story
                </button>

            </div>

        </form>

    </div>

</section>

<script>
const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('imagePreview');
const imagePreviewContainer = document.getElementById('imagePreviewContainer');
const selectedImageName = document.getElementById('selectedImageName');

imageInput.addEventListener('change', function () {

    const file = this.files[0];

    if (!file) {
        imagePreviewContainer.style.display = 'none';
        selectedImageName.textContent = '';
        return;
    }

    // Check that the selected file is an image
    if (!file.type.startsWith('image/')) {
        imageInput.value = '';
        imagePreviewContainer.style.display = 'none';
        selectedImageName.textContent = '';
        alert('Please select a valid image file.');
        return;
    }

    // Show selected file name
    selectedImageName.textContent = 'Selected: ' + file.name;

    // Create image preview
    const reader = new FileReader();

    reader.onload = function (event) {
        imagePreview.src = event.target.result;
        imagePreviewContainer.style.display = 'block';
    };

    reader.readAsDataURL(file);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>