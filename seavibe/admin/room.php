<?php
session_start();
include '../config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BlueBird - Admin</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>

  <style>
    body {
      background: #f8f9fa;
      font-family: Arial, sans-serif;
    }

    .form-section {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      margin: 30px auto;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      max-width: 700px;
    }

    .form-section h4 {
      color: #0d6efd;
      font-weight: 600;
    }

    .card-room {
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
      text-align: center;
      padding: 25px 20px;
    }

    .card-room:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .card-room .icon {
      font-size: 3.5rem;
      color: #0d6efd;
      margin-bottom: 15px;
    }

    .card-room h5 {
      font-size: 1.3rem;
      color: #333;
      margin-bottom: 10px;
    }

    .card-room p {
      color: #555;
      font-size: 1.1rem;
      margin-bottom: 20px;
    }

    .card-room .btn-danger {
      margin-top: auto;
    }

    /* ✅ Prevent cutoff at bottom */
    .container {
      padding-bottom: 80px;
    }
  </style>
</head>

<body>

<div class="container">
  <!-- Add Room Form -->
  <div class="form-section">
    <h4 class="mb-3"><i class="fa-solid fa-plus-circle text-success"></i> Add Room</h4>
    <form action="" method="POST" class="row g-3">
      <div class="col-md-6">
        <label for="troom" class="form-label">Type of Room</label>
        <select name="troom" id="troom" class="form-select" required>
          <option value selected disabled>Choose...</option>
          <option value="Superior Room">Superior Room</option>
          <option value="Deluxe Room">Deluxe Room</option>
          <option value="Guest House">Guest House</option>
          <option value="Single Room">Single Room</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="bed" class="form-label">Type of Bed</label>
        <select name="bed" id="bed" class="form-select" required>
          <option value selected disabled>Choose...</option>
          <option value="Single">Single</option>
          <option value="Double">Double</option>
          <option value="Triple">Triple</option>
          <option value="Quad">Quad</option>
          <option value="None">None</option>
        </select>
      </div>
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-success" name="addroom"><i class="fa-solid fa-check"></i> Add Room</button>
      </div>
    </form>
  </div>

  <?php
  if (isset($_POST['addroom'])) {
      $typeofroom = $_POST['troom'];
      $typeofbed = $_POST['bed'];

      $sql = "INSERT INTO room(type,bedding) VALUES ('$typeofroom', '$typeofbed')";
      $result = mysqli_query($conn, $sql);

      if ($result) {
          header("Location: room.php");
      }
  }
  ?>

  <!-- Room Cards -->
  <div class="row g-4">
    <?php
    $sql = "SELECT * FROM room";
    $re = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_array($re)) {
    ?>
      <div class="col-md-4 d-flex">
        <div class="card-room w-100">
          <i class="fa-solid fa-bed icon"></i>
          <h5><?php echo htmlspecialchars($row['type']); ?></h5>
          <p><?php echo htmlspecialchars($row['bedding']); ?></p>
          <a href="roomdelete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">
            <i class="fa-solid fa-trash"></i> Delete
          </a>
        </div>
      </div>
    <?php
    }
    ?>
  </div>
</div>

</body>
</html>
