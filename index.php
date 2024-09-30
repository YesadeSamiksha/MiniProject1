<?php
    session_start();

    // Configuration
    $db_host = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "test";

    // Connect to the database
    $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Handle form submission for signup or login
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['signup'])) {
            // Signup logic
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);

            // Check if email already exists
            $check_email_query = "SELECT * FROM registration WHERE email='$email'";
            $check_email_result = mysqli_query($conn, $check_email_query);

            if (mysqli_num_rows($check_email_result) > 0) {
                echo "<script>alert('Email already exists. Please try logging in.');</script>";
            } else {
                // Insert new user into the registration table
                $signup_query = "INSERT INTO registration (name, email, password) VALUES ('$name', '$email', '$password')";

                if (mysqli_query($conn, $signup_query)) {
                    echo "<script>alert('Signup successful! Please log in.');</script>";
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            }
        } elseif (isset($_POST['login'])) {
            // Login logic
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);

            // Fetch the user record from the registration table
            $login_query = "SELECT * FROM registration WHERE email='$email'";
            $login_result = mysqli_query($conn, $login_query);

            if (mysqli_num_rows($login_result) > 0) {
                $user = mysqli_fetch_assoc($login_result);

                // Verify the password
                if ($password == $user['password']) {
                    // Set session variable to indicate user is logged in
                    $_SESSION['loggedin'] = true;
                    $_SESSION['name'] = $user['name']; // Store user name in session
                } else {
                    echo "<script>alert('Invalid password. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('No account found with that email. Please sign up.');</script>";
            }
        }
    }

    // Handle logout
    if (isset($_GET['logout'])) {
        session_unset(); // Unset session variables
        session_destroy(); // Destroy the session
        header("Location: " . $_SERVER["PHP_SELF"]); // Redirect to the same page
        exit;
    }

    // Close database connection
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorex</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif; 
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .header {
            background: linear-gradient(#03010d,#100b4d,#1f318d, #3a3b82);
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }

        .navbar {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            transition: 0.3s;
        }

        .navbar a:hover {
            background: #100b4d;
            border-radius: 5px;
        }

        .form-container {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .form {
            display: none;
        }

        .form.active {
            display: block;
        }

        .form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
        }

        .form button {
            width: 100%;
            padding: 10px;
            background: blue;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }

        .form button:hover {
            background-color: #100b4d;
            color: aqua;
        }

        .form p {
            text-align: center;
        }

        .hidden {
            display: none !important;
        }

        .visible {
            display: block !important;
        }

        .services {
            display: none;
            padding: 50px;
        }
        
        .services.visible {
            display: block;
        }

        .logout-btn {
            color: #fff;
            background: red;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: darkred;
        }

        /* Styles for home section */
        .home {
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .home video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="#" class="logo">Explorex</a>
        <p class="slogan">Where-ever You Roam, We Guide</p>
        <nav class="navbar">
            <a href="#home" class="active">Home</a>
            <a href="#services">Services</a>
            <a href="#search">Search</a>
            <a href="#contact">Feedback</a>
            <!-- Display logout button if the user is logged in -->
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                <a href="?logout" class="logout-btn">Logout</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Home Section with conditional background -->
    <section class="home" id="home" style="<?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ? 'background: none;' : 'background-image: url(img1.avif);'; ?>">
        <!-- Video background for logged-in users -->
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <video autoplay muted loop>
                <source src="video.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="welcome-message">
                <h2>Welcome, <?php echo $_SESSION['name']; ?>!</h2>
                <p>You have successfully logged in. Explore our services below.</p>
            </div>
        <?php endif; ?>
        
        <!-- Display form container if not logged in -->
        <div class="form-container <?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ? 'hidden' : ''; ?>">
            <!-- Login Form -->
            <form id="login-form" class="form active" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
                <h2>Login</h2>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>New user? <a href="#" id="show-signup">Sign Up</a></p>
            </form>

            <!-- Signup Form -->
            <form id="signup-form" class="form" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
                <h2>Sign Up</h2>
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="signup">Sign Up</button>
                <p>Already have an account? <a href="#" id="show-login">Login</a></p>
            </form>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services <?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ? 'visible' : ''; ?>">
        <h2 class="heading">Our&nbsp;<span>Services</span></h2>
        <div class="services-container">
            <div class="services-box">
                <h3>Route</h3>
                <p>Lorem ipsum dolor sit amet consectetur.</p>
                <a href="index.html" class="btn">Read More</a>
            </div>
            <div class="services-box">
                <h3>Search</h3>
                <p>Lorem ipsum dolor sit amet consectetur.</p>
                <a href="map.html" class="btn">Read More</a>
            </div>
            <div class="services-box">
                <h3>Favourites</h3>
                <p>Lorem ipsum dolor sit amet consectetur.</p>
                <a href="#" class="btn">Read More</a>
            </div>
        </div>
        <div class="info">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
            <a href="#" class="btnn">Read More</a>
        </div>
    </section>

    <section class="contact" id="contact">
        <h2 class="heading">Feedback</h2>
        <form action="#">
        <div class="contact-form">
            <input type="text" placeholder="Name">
            <input type="email" placeholder="Email">
            
        </div>
        <div class="contact-form">
            <input type="number" placeholder="Mobile Number">
            
        </div>
        <textarea name="" id="" cols="" rows="" placeholder="Your Message" ></textarea> 
        <input type="submit" value="Send Message" class="btn">
         </form>

    </section>

    <footer class="footer">
        <div class="social-media">
            <a href="#"><i class='bx bxl-instagram'></i></a>
            <a href="#"><i class='bx bxl-facebook'></i></a>
            <a href="#"><i class='bx bxl-youtube'></i></a>
            <a href="#"><i class='bx bxl-twitter'></i></a>
        </div>
    </footer>

    <script>
        // Toggle between signup and login forms
        document.getElementById('show-signup').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('login-form').classList.remove('active');
            document.getElementById('signup-form').classList.add('active');
        });

        document.getElementById('show-login').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('signup-form').classList.remove('active');
            document.getElementById('login-form').classList.add('active');
        });
    </script>
</body>
</html>



   