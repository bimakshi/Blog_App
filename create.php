<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Only logged-in users can create stories
require_login();

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

    $image_name = null;

    // Validate title and content
    if ($title === '' || $content === '') {

        set_flash(
            'error',
            'Please enter both a story title and content.'
        );

    // Validate category
    } elseif (!$category_id) {

        set_flash(
            'error',
            'Please select a category.'
        );

    // Validate image exists
    } elseif (
        !isset($_FILES['image']) ||
        $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE
    ) {

        set_flash(
            'error',
            'Please select a cover image for your story.'
        );

    } else {

        $image = $_FILES['image'];

        // Check upload error
        if ($image['error'] !== UPLOAD_ERR_OK) {

            set_flash(
                'error',
                'There was a problem uploading your image.'
            );

        } else {

            // Maximum image size: 5 MB
            $max_size = 5 * 1024 * 1024;

            // Allowed image extensions
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

            // Check file size
            if ($image['size'] > $max_size) {

                set_flash(
                    'error',
                    'Image size must be 5 MB or smaller.'
                );

            // Check extension
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

                // Verify that the file is actually an image
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
                    $image_name =
                        bin2hex(random_bytes(16))
                        . '.'
                        . $extension;

                    $upload_directory =
                        __DIR__ . '/uploads/blogs/';

                    // Create directory if it doesn't exist
                    if (!is_dir($upload_directory)) {

                        mkdir(
                            $upload_directory,
                            0755,
                            true
                        );
                    }

                    $upload_path =
                        $upload_directory . $image_name;

                    // Move image
                    if (!move_uploaded_file(
                        $image['tmp_name'],
                        $upload_path
                    )) {

                        set_flash(
                            'error',
                            'Unable to save the uploaded image.'
                        );

                        $image_name = null;
                    }
                }
            }
        }


        // Insert blog only when image upload succeeded
        if ($image_name !== null) {

            $stmt = $pdo->prepare(
                "INSERT INTO blogs
                (user_id, category_id, title, content, image)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                current_user_id(),
                $category_id,
                $title,
                $content,
                $image_name
            ]);

            set_flash(
                'success',
                'Your story has been published.'
            );

            redirect('myblogs.php');
        }
    }
}


// Get categories
$category_stmt = $pdo->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

$categories = $category_stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="write-page">

    <div class="write-header">

        <span class="write-label">
            CREATE SOMETHING
        </span>

        <h2>
            Write Your Story
        </h2>

        <p>
            Share your ideas, experiences, and thoughts with the BlogNest community.
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
                    value="<?= sanitize($_POST['title'] ?? '') ?>"
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
                                isset($_POST['category_id'])
                                && $_POST['category_id'] == $category['id']
                            ) ? 'selected' : '' ?>
                        >
                            <?= sanitize($category['name']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Cover Image -->

            <div class="form-group">

    <label for="image">
        Cover Image <span>*</span>
    </label>

    <label
        for="image"
        class="image-upload-box"
    >

        <span class="image-upload-icon">
            ↑
        </span>

        <span class="image-upload-title">
            Choose a cover image
        </span><br>

        <span class="image-upload-text">
            JPG, JPEG, PNG or WebP
        </span><br>

        <span class="image-upload-size">
            Maximum size: 5 MB
        </span>

    </label>

    <input
        type="file"
        id="image"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
        required
        class="image-file-input"
    >

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
                ><?= sanitize($_POST['content'] ?? '') ?></textarea>

            </div>


            <!-- Actions -->

            <div class="write-actions">

                <span class="draft-note">
                    Your story will be published immediately.
                </span>

                <button
                    type="submit"
                    class="publish-btn"
                >
                    Publish Story
                </button>

            </div>

        </form>

    </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>