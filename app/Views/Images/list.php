<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Uploaded Images</title>
  <link rel="stylesheet" href="<?= base_url('assets/styles/imagesstyles.css'); ?>">
</head>
<body>
  <?= $this->include('layouts/sidebar') ?>

  <div class="uploaded-images">
    <h2>Uploaded Images</h2>

    <?php if(empty($files)): ?>
      <p>No images uploaded yet.</p>
    <?php else: ?>
      <div class="images-grid">
        <?php foreach($files as $file): ?>
          <div class="image-item">
            <img src="<?= base_url('upload/' . $file) ?>" alt="Uploaded Image" width="200">
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <a href="<?= site_url('images') ?>" class="large-button-outline">
      <span class="button-2">Back to Upload</span>
    </a>
  </div>
</body>
</html>
