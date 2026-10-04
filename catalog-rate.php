<?php
// ============================================================================
// catalog-rate.php — password-protected page (shared password, server-side)
// ----------------------------------------------------------------------------
// HOW TO CHANGE THE PASSWORD (on any machine with PHP):
//   php -r "echo password_hash('Your-New-Password-Here', PASSWORD_DEFAULT), PHP_EOL;"
// Copy the output ($2y$...) and paste it below as PASSWORD_HASH.
// ============================================================================
define('PASSWORD_HASH', '$2y$12$BTHx86j/QuJyiLFaj2ZNlevazHjf/g/7gS6beU2Hw8ycH1dlUMKX2');

// ---- Secure session cookie (same pattern as csrf-token.php / form_send.php) ----
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params(array(
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ));
}
session_start();

// ---- Never cache / never index the protected page ----
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Robots-Tag: noindex, nofollow');

// ---- CSRF token for the login form ----
if (empty($_SESSION['catalog_csrf'])) {
    $_SESSION['catalog_csrf'] = bin2hex(random_bytes(32));
}

// ---- Simple brute-force throttle: 5 failures -> 15 min lockout ----
if (!isset($_SESSION['catalog_attempts'])) {
    $_SESSION['catalog_attempts'] = 0;
}
$now = time();
$locked_remaining = 0;
if (!empty($_SESSION['catalog_locked_until']) && $_SESSION['catalog_locked_until'] > $now) {
    $locked_remaining = $_SESSION['catalog_locked_until'] - $now;
}

$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['catalog_password'])) {
    if ($locked_remaining > 0) {
        $login_error = 'Too many attempts. Try again in ' . ceil($locked_remaining / 60) . ' min.';
    } elseif (empty($_POST['catalog_csrf']) || !hash_equals((string) $_SESSION['catalog_csrf'], (string) $_POST['catalog_csrf'])) {
        http_response_code(403);
        $login_error = 'Invalid session. Please reload and try again.';
    } else {
        $attempt = (string) $_POST['catalog_password'];
        if (password_verify($attempt, PASSWORD_HASH)) {
            // Optional transparent rehash if PHP upgrades the default algorithm.
            // (Rehash needs the plaintext attempt, so it must happen here.)
            // Future maintainers: if you rotate PASSWORD_HASH, nothing else to change.
            $_SESSION['catalog_authed'] = true;
            $_SESSION['catalog_attempts'] = 0;
            unset($_SESSION['catalog_locked_until']);
            unset($_SESSION['catalog_csrf']); // rotate CSRF token after login
            session_regenerate_id(true);
            // PRG pattern: avoid password resubmission on refresh.
            header('Location: ' . $_SERVER['PHP_SELF'], true, 303);
            exit;
        } else {
            $_SESSION['catalog_attempts']++;
            if ($_SESSION['catalog_attempts'] >= 5) {
                $_SESSION['catalog_locked_until'] = $now + (15 * 60);
                $login_error = 'Too many attempts. Locked for 15 minutes.';
            } else {
                // Generic message on purpose (do not reveal which part failed).
                $login_error = 'Incorrect password. Please try again.';
            }
            // Small delay to slow automated guessing.
            usleep(500000);
        }
    }
}

// Logout via ?logout=1 (also see catalog-logout.php for a dedicated URL).
if (isset($_GET['logout'])) {
    unset($_SESSION['catalog_authed']);
    session_regenerate_id(true);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'), true, 303);
    exit;
}

$isAuthed = !empty($_SESSION['catalog_authed']);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title><?php echo $isAuthed ? 'Catalog Rates | Medley Networks' : 'Restricted Access | Medley Networks'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if (!$isAuthed): ?>
  <meta name="robots" content="noindex, nofollow">
  <?php endif; ?>
  <link href='https://fonts.googleapis.com/css?family=Raleway:900,300,700' rel='stylesheet' type='text/css'>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/images/logos/logo1.png">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="css/layout.css">
  <link rel="stylesheet" type="text/css" href="css/responsive.css">
  <link rel="stylesheet" type="text/css" href="css/animate.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/site.css?v=20260925">

  <style>
    body {
      padding-top: 70px;
    }
    .rate-table th {
      background: #222;
      color: #fff;
      white-space: nowrap;
    }
    .rate-table td:nth-child(1),
    .rate-table th:nth-child(1),
    .rate-table td:nth-child(5),
    .rate-table th:nth-child(5) {
      text-align: center;
      white-space: nowrap;
    }
    .rate-table td:nth-child(4),
    .rate-table th:nth-child(4) {
      text-align: right;
      white-space: nowrap;
    }
    /* Long catalog: scroll within the card with a sticky header. */
    .rate-table-scroll {
      max-height: 70vh;
      overflow-y: auto;
    }
    .rate-table-scroll thead th {
      position: sticky;
      top: 0;
      z-index: 1;
    }
    .login-card {
      max-width: 440px;
      margin: 60px auto;
    }
    /* Flexbox wrapper (instead of Bootstrap input-group) so the password
       field and Show/Hide button are always exactly the same height.
       NOTE: css/site.css overrides .btn with larger padding (10px 25px),
       so the toggle needs explicit metrics to match .form-control (34px). */
    .catalog-password-wrap {
      display: flex;
      align-items: stretch;
    }
    .catalog-password-wrap .form-control {
      min-width: 0;
      height: 34px;
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
    }
    .catalog-password-wrap #toggle-catalog-password {
      flex: 0 0 auto;
      white-space: nowrap;
      height: 34px;
      box-sizing: border-box;
      padding: 6px 12px;
      font-size: 14px;
      line-height: 1.42857143;
      margin-left: -1px;
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
    }
  </style>
</head>

<body>

  <!-- Navigation -->
  <section id="navigation">
    <nav role="navigation" class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" class="navbar-toggle"
            type="button">&nbsp;</button>
          <a style="margin: 0px; padding: 5px 0px 0px; vertical-align: middle !important;" class="navbar-brand"
            href="index.html#home">
            <img alt=""
              style="height: 40px; width: 100px; margin: 0px; padding: 0px; vertical-align: middle !important;"
              src="assets/images/logos/logo-L.webp" decoding="async" width="1012" height="385">
          </a>
        </div>
        <div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
          <ul style="vertical-align: bottom !important;" class="nav navbar-nav navbar-right">
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="index.html#home">Home</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="index.html#services">Services</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="/rf-engineering.html">RF Engineering</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="/rf-compliance.html">RF Compliance</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="/staffing.html">Staffing</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="/it-services.html">IT Services</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="/emerging-technologies.html">Emerging Technologies</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="index.html#store">Store</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="index.html#ContactUs">Contact Us</a></li>
            <li style="font-size: 14px; font-family: Arial, sans-serif;"><a href="/privacy-policy.html">Privacy Policy</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </section>

  <div id="catalog-rate" class="intro">
    <div class="container">
      <div class="section">

<?php if (!$isAuthed): ?>
        <div class="login-card">
          <div class="eyebrow">Restricted</div>
          <div class="accent-bar"></div>
          <h2 class="section-title t-title">Catalog Rates</h2>
          <p class="section-subtitle t-lead">This page is password protected. Enter the password to continue.</p>

          <div class="card card--tint card--pad-lg">
            <?php if ($login_error !== ''): ?>
              <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
              <input type="hidden" name="catalog_csrf" value="<?php echo htmlspecialchars($_SESSION['catalog_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
              <div class="form-group">
                <label for="catalog_password" class="t-body"><strong>Password</strong></label>
                <div class="catalog-password-wrap">
                  <input type="password" id="catalog_password" name="catalog_password" class="form-control"
                    required autofocus autocomplete="current-password" <?php echo $locked_remaining > 0 ? 'disabled' : ''; ?>>
                  <button type="button" id="toggle-catalog-password" class="btn btn-default"
                    aria-label="Show password" aria-pressed="false" <?php echo $locked_remaining > 0 ? 'disabled' : ''; ?>>Show</button>
                </div>
              </div>
              <button type="submit" class="btn t-btn" <?php echo $locked_remaining > 0 ? 'disabled' : ''; ?>>Unlock &rarr;</button>
            </form>
          </div>
        </div>
<?php else: ?>
        <div>
          <div class="eyebrow">Catalog</div>
          <div class="accent-bar"></div>
          <h2 class="section-title t-title">Catalog Rates</h2>
        </div>

        <div class="card card--tint card--pad-lg">
          <div class="table-responsive rate-table-scroll">
            <table class="table table-bordered table-striped rate-table">
              <thead>
                <tr>
                  <th>S. No.</th>
                  <th>Category</th>
                  <th>Service</th>
                  <th>Catalog (List) Rate</th>
                  <th>Rate Type</th>
                </tr>
              </thead>
              <tbody>
                <tr><td>1</td><td>Administrative Staffing</td><td>Accountant</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;38.00</td><td>Hourly</td></tr>
                <tr><td>2</td><td>Administrative Staffing</td><td>Accounts Receivable Clerk</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;30.00</td><td>Hourly</td></tr>
                <tr><td>3</td><td>Administrative Staffing</td><td>Administrative HR Specialist</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;38.00</td><td>Hourly</td></tr>
                <tr><td>4</td><td>Administrative Staffing</td><td>Buyer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;41.00</td><td>Hourly</td></tr>
                <tr><td>5</td><td>Administrative Staffing</td><td>Contract Administrator</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;47.00</td><td>Hourly</td></tr>
                <tr><td>6</td><td>Administrative Staffing</td><td>Data Entry</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;28.00</td><td>Hourly</td></tr>
                <tr><td>7</td><td>Administrative Staffing</td><td>Financial Analyst</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;51.00</td><td>Hourly</td></tr>
                <tr><td>8</td><td>Administrative Staffing</td><td>Grant Administrator</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;47.00</td><td>Hourly</td></tr>
                <tr><td>9</td><td>Administrative Staffing</td><td>HR Manager</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;61.00</td><td>Hourly</td></tr>
                <tr><td>10</td><td>Administrative Staffing</td><td>Medical Assistant</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;35.00</td><td>Hourly</td></tr>
                <tr><td>11</td><td>Administrative Staffing</td><td>Medical Biller</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;34.00</td><td>Hourly</td></tr>
                <tr><td>12</td><td>Administrative Staffing</td><td>Payroll Analyst</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;47.00</td><td>Hourly</td></tr>
                <tr><td>13</td><td>Administrative Staffing</td><td>Payroll Manager</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;61.00</td><td>Hourly</td></tr>
                <tr><td>14</td><td>Administrative Staffing</td><td>Payroll Specialist</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;41.00</td><td>Hourly</td></tr>
                <tr><td>15</td><td>Administrative Staffing</td><td>Procurement Specialist</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;43.00</td><td>Hourly</td></tr>
                <tr><td>16</td><td>Administrative Staffing</td><td>Project Coordinator</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;41.00</td><td>Hourly</td></tr>
                <tr><td>17</td><td>Administrative Staffing</td><td>Receptionist</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;27.00</td><td>Hourly</td></tr>
                <tr><td>18</td><td>Services</td><td>Design consultation and engineering services</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;140.00</td><td>Hourly</td></tr>
                <tr><td>19</td><td>Services</td><td>Antenna System Intermodulation study services</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;160.00</td><td>Hourly</td></tr>
                <tr><td>20</td><td>Services</td><td>RF coverage &amp; feasibility consultation services &amp; software</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;140.00</td><td>Hourly</td></tr>
                <tr><td>21</td><td>Services</td><td>Antenna system design testing and installation training</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;145.00</td><td>Hourly</td></tr>
                <tr><td>22</td><td>Technology Staffing</td><td>Computer Programmer Analyst I</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;41.15</td><td>Hourly</td></tr>
                <tr><td>23</td><td>Technology Staffing</td><td>Computer Programmer Analyst II</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;45.35</td><td>Hourly</td></tr>
                <tr><td>24</td><td>Technology Staffing</td><td>Computer Programmer Analyst III</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;50.00</td><td>Hourly</td></tr>
                <tr><td>25</td><td>Technology Staffing</td><td>Computer Programmer Analyt IV</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;52.50</td><td>Hourly</td></tr>
                <tr><td>26</td><td>Technology Staffing</td><td>Database Administrator I</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;50.00</td><td>Hourly</td></tr>
                <tr><td>27</td><td>Technology Staffing</td><td>Database Administrator II</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;52.50</td><td>Hourly</td></tr>
                <tr><td>28</td><td>Technology Staffing</td><td>Network Engineer I</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;39.20</td><td>Hourly</td></tr>
                <tr><td>29</td><td>Technology Staffing</td><td>Network Engineer II</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;43.20</td><td>Hourly</td></tr>
                <tr><td>30</td><td>Technology Staffing</td><td>Network Engineer III</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;52.50</td><td>Hourly</td></tr>
                <tr><td>31</td><td>Technology Staffing</td><td>Systems Engineer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>32</td><td>Technology Staffing</td><td>Sr. Systems Engineer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;94.50</td><td>Hourly</td></tr>
                <tr><td>33</td><td>Technology Staffing</td><td>Applications Developer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;84.00</td><td>Hourly</td></tr>
                <tr><td>34</td><td>Technology Staffing</td><td>Applications Developer Architect</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;97.00</td><td>Hourly</td></tr>
                <tr><td>35</td><td>Technology Staffing</td><td>Applications Developer - Senior</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;88.00</td><td>Hourly</td></tr>
                <tr><td>36</td><td>Technology Staffing</td><td>Back-End Developer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>37</td><td>Technology Staffing</td><td>Boomi Developer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>38</td><td>Technology Staffing</td><td>Business Analyst</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;54.00</td><td>Hourly</td></tr>
                <tr><td>39</td><td>Technology Staffing</td><td>Business Intelligence (BI) Developer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>40</td><td>Technology Staffing</td><td>Cloud Application Developer - Principal</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;101.00</td><td>Hourly</td></tr>
                <tr><td>41</td><td>Technology Staffing</td><td>Cloud Data Architect - Senior</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;111.00</td><td>Hourly</td></tr>
                <tr><td>42</td><td>Technology Staffing</td><td>Cloud Database Administrator - Mid</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>43</td><td>Technology Staffing</td><td>Cloud Infrastructure Engineer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;88.00</td><td>Hourly</td></tr>
                <tr><td>44</td><td>Technology Staffing</td><td>Cloud Solutions Architect - Senior</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;111.00</td><td>Hourly</td></tr>
                <tr><td>45</td><td>Technology Staffing</td><td>Data Analyst</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;57.00</td><td>Hourly</td></tr>
                <tr><td>46</td><td>Technology Staffing</td><td>Data Architect</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;101.00</td><td>Hourly</td></tr>
                <tr><td>47</td><td>Technology Staffing</td><td>Data Reporting Engineer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>48</td><td>Technology Staffing</td><td>Data Reporting Engineer - Senior</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;95.00</td><td>Hourly</td></tr>
                <tr><td>49</td><td>Technology Staffing</td><td>Desktop Support Engineer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;41.00</td><td>Hourly</td></tr>
                <tr><td>50</td><td>Technology Staffing</td><td>Electrical Engineer (PE)</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;68.00</td><td>Hourly</td></tr>
                <tr><td>51</td><td>Technology Staffing</td><td>Front-End Developer (React / Angular / UI Developer)</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;86.00</td><td>Hourly</td></tr>
                <tr><td>52</td><td>Technology Staffing</td><td>Full Stack Developer (Java / .NET / Python)</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;95.00</td><td>Hourly</td></tr>
                <tr><td>53</td><td>Technology Staffing</td><td>GIS Technician</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;34.00</td><td>Hourly</td></tr>
                <tr><td>54</td><td>Technology Staffing</td><td>Help Desk Support Services Specialist</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;34.00</td><td>Hourly</td></tr>
                <tr><td>55</td><td>Technology Staffing</td><td>Infrastructure Engineer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>56</td><td>Technology Staffing</td><td>IT Project Manager</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;88.00</td><td>Hourly</td></tr>
                <tr><td>57</td><td>Technology Staffing</td><td>IT Support Technician</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;43.00</td><td>Hourly</td></tr>
                <tr><td>58</td><td>Technology Staffing</td><td>LAN Administrator - Intermediate</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;54.00</td><td>Hourly</td></tr>
                <tr><td>59</td><td>Technology Staffing</td><td>LAN Administrator - Senior</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;68.00</td><td>Hourly</td></tr>
                <tr><td>60</td><td>Technology Staffing</td><td>Mobile App Developer (iOS / Android)</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;68.00</td><td>Hourly</td></tr>
                <tr><td>61</td><td>Technology Staffing</td><td>Multimedia Designer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;61.00</td><td>Hourly</td></tr>
                <tr><td>62</td><td>Technology Staffing</td><td>Multimedia Developer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;68.00</td><td>Hourly</td></tr>
                <tr><td>63</td><td>Technology Staffing</td><td>Network Administrator</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;65.00</td><td>Hourly</td></tr>
                <tr><td>64</td><td>Technology Staffing</td><td>Network Engineer - Intermediate</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;74.00</td><td>Hourly</td></tr>
                <tr><td>65</td><td>Technology Staffing</td><td>Network Engineer - Senior</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;88.00</td><td>Hourly</td></tr>
                <tr><td>66</td><td>Technology Staffing</td><td>Oracle DBA (Security)</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;88.00</td><td>Hourly</td></tr>
                <tr><td>67</td><td>Technology Staffing</td><td>Program Manager</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;74.00</td><td>Hourly</td></tr>
                <tr><td>68</td><td>Technology Staffing</td><td>Architect - Senior</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;111.00</td><td>Hourly</td></tr>
                <tr><td>69</td><td>Technology Staffing</td><td>Software Engineer - Junior</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>70</td><td>Technology Staffing</td><td>Software Engineer - Mid</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;101.00</td><td>Hourly</td></tr>
                <tr><td>71</td><td>Technology Staffing</td><td>Software Engineer - Principal</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;115.00</td><td>Hourly</td></tr>
                <tr><td>72</td><td>Technology Staffing</td><td>SQL DBA System Administrator</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;81.00</td><td>Hourly</td></tr>
                <tr><td>73</td><td>Technology Staffing</td><td>Web Designer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;54.00</td><td>Hourly</td></tr>
                <tr><td>74</td><td>Technology Staffing</td><td>Web Security Administrator</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;68.00</td><td>Hourly</td></tr>
                <tr><td>75</td><td>Technology Staffing</td><td>Web Security Analyst</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;74.00</td><td>Hourly</td></tr>
                <tr><td>76</td><td>Technology Staffing</td><td>Web Software Developer</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;68.00</td><td>Hourly</td></tr>
                <tr><td>77</td><td>Technology Staffing</td><td>Web Support Administrator</td><td>$&nbsp;&nbsp;&nbsp;&nbsp;54.00</td><td>Hourly</td></tr>
              </tbody>
            </table>
          </div>
          <p class="t-body" style="margin-top: 15px;">
            <a href="?logout=1">Lock this page (log out)</a>
          </p>
        </div>
<?php endif; ?>

      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container">
      <a href="#"><img alt="" src="assets/images/logos/logo1.webp" width="85" height="80" decoding="async"></a>
      <p class="t-body">2026 &copy; <a href="#">Medley Networks</a> All Rights Reserved.</p>
    </div>
  </footer>

  <script src="js/jquery-3.7.1.min.js" integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs" crossorigin="anonymous"></script>
  <script src="js/waypoints.min.js"></script>
  <script src="js/script.js?v=20260925"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/catalog-rate.js?v=20260926"></script>
</body>

</html>
