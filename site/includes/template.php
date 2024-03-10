<?php
  require_once('config.php');

  function get_internal_url($script) {
    return $script . '?v=' . hash_file('md5', realpath(dirname(__FILE__)) . '/../web' . $script);
  }

  function render_header() {
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo CON_NAME; ?> Member Portal</title>
    <script src="https://unpkg.com/htmx.org@1.9.10" integrity="sha384-D1Kt99CQMDuVetoL1lrYwg5t+9QdHe7NLX/SoJYkXDFfX37iInKRy5xLSi8nO7UC" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/htmx.org/dist/ext/loading-states.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>@font-face{font-family:Rubik;font-style:italic;font-weight:300;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWbBXyIfDnIV7nEt3KSJbVDV49rz8sDE3U5f4L1kA.woff2) format('woff2');unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:Rubik;font-style:italic;font-weight:300;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWbBXyIfDnIV7nEt3KSJbVDV49rz8sDE3U3f4I.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:Rubik;font-style:italic;font-weight:400;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWbBXyIfDnIV7nEt3KSJbVDV49rz8tdE3U5f4L1kA.woff2) format('woff2');unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:Rubik;font-style:italic;font-weight:400;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWbBXyIfDnIV7nEt3KSJbVDV49rz8tdE3U3f4I.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:Rubik;font-style:italic;font-weight:700;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWbBXyIfDnIV7nEt3KSJbVDV49rz8u6FHU5f4L1kA.woff2) format('woff2');unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:Rubik;font-style:italic;font-weight:700;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWbBXyIfDnIV7nEt3KSJbVDV49rz8u6FHU3f4I.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:Rubik;font-style:normal;font-weight:300;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWZBXyIfDnIV5PNhY1KTN7Z-Yh-WYiFWUU1Z4Y.woff2) format('woff2');unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:Rubik;font-style:normal;font-weight:300;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWZBXyIfDnIV5PNhY1KTN7Z-Yh-WYiFV0U1.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:Rubik;font-style:normal;font-weight:400;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWZBXyIfDnIV5PNhY1KTN7Z-Yh-B4iFWUU1Z4Y.woff2) format('woff2');unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:Rubik;font-style:normal;font-weight:400;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWZBXyIfDnIV5PNhY1KTN7Z-Yh-B4iFV0U1.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}@font-face{font-family:Rubik;font-style:normal;font-weight:700;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWZBXyIfDnIV5PNhY1KTN7Z-Yh-4I-FWUU1Z4Y.woff2) format('woff2');unicode-range:U+0100-024F,U+0259,U+1E00-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF}@font-face{font-family:Rubik;font-style:normal;font-weight:700;font-display:swap;src:url(https://fonts.gstatic.com/s/rubik/v20/iJWZBXyIfDnIV5PNhY1KTN7Z-Yh-4I-FV0U1.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}</style>
    <link rel="shortcut icon" href="/resources/favicon.png">
    <link rel="stylesheet" href="<?php echo get_internal_url("/resources/reset.css"); ?>">
    <link rel="stylesheet" href="<?php echo get_internal_url("/resources/style.css"); ?>">
  </head>
  <body>
    <header>
      <div class="container">
        <div class="site-branding-container">
          <div class="site-logo">
					  <a href="/" rel="home"><img alt="Levitation 2024" src="https://eastercon2024.co.uk/assets/uploads/Yellow-black-green-logo@1x.png" srcset="https://eastercon2024.co.uk/assets/uploads/Yellow-black-green-logo@1x.png 1x, https://eastercon2024.co.uk/assets/uploads/Yellow-black-green-logo@2x.png 2x" width="50" height="70"></a>
				  </div>
          <div class="site-branding">
						<h1><a href="/" rel="home">Levitation 2024</a></h1>
            <h2><a href="/" rel="home">Member portal</a></h2>
				  </div>
        </div>
        <?php
          if (is_logged_in()) {
        ?>
          <p class="username"><?php echo get_current_user_name(); ?> | <a href="/logout/">Logout</a></p>
        <?php
          }
        ?>
      </div>
    </header>
    <main>
<?php
  }

  function render_footer() {
?>
    </main>
    <footer>
      <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php echo CON_NAME; ?>. Artwork by <a href="https://www.facebook.com/SpaceArtistJackieBurns/">Jackie Burns</a></p>
      </div>
    </footer>
  </body>
</html>
<?php
  }
?>