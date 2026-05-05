<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Apply - Starlight Institute</title>
  <link rel="stylesheet" href="../css/style-2.css">
</head>
<body class="dashboard-body">

  <header class="school-top-header">
    <img src="../logo.png" alt="Starlight Institute Logo" class="school-logo">
    <span class="school-name">Starlight Institute</span>
    <nav class="top-nav">
      <a href="../index.php" class="top-link">Home</a>
      <a href="admission.php" class="top-link">Back</a>
    </nav>
  </header>

  <div class="dashboard-content">
    <main class="dashboard-main">
      <section class="form-section">
        <h2>Admission Application Form</h2>
        <form action="process_application.php" method="post" class="styled-form">

          <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" required>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required>
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" required>
          </div>

          <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob" required>
          </div>
          <div class="form-group">
          <label for="gender">Gender:</label>
            <select name="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="female">Female</option>
                <option value="male">Male</option>
                <option value="prefer_not_to_say">Prefer not to say</option>
            </select> <br>
          </div>
          <div class="form-group">
            <label for="program">Program Interested In</label>
            <select name="program" id="program" required>
              <option value="">-- Select Program --</option>
              <option value="Computer Science">Computer Science</option>
              <option value="Information Technology">Information Technology</option>
              <option value="Business Management">Business Management</option>
              <option value="Education">Education</option>
              <!-- Add more programs as needed -->
            </select>
          </div>

          <div class="form-group">
            <label for="address">Home Address</label>
            <textarea name="address" id="address" rows="3" required></textarea>
          </div>

          <div class="form-group">
            <label for="kcse_index">KCSE Index Number</label>
            <input type="text" name="kcse_index" id="kcse_index" required>
          </div>

          <div class="form-group">
            <label for="kcse_grade">KCSE Mean Grade</label>
            <input type="text" name="kcse_grade" id="kcse_grade" required>
          </div>

          <button type="submit" class="btn-primary">Submit Application</button>
        </form>
      </section>
    </main>
  </div>

</body>
</html>
