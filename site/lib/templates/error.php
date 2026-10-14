<?php
// Rendered by exception_handler in config.php with $err_code set
$mailto = 'mailto:' . SUPPORT_EMAIL
  . '?subject=' . rawurlencode('Problem with member portal')
  . '&body=' . rawurlencode("Code: $err_code\n\nProblem:\n");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error - <?php echo htmlspecialchars(CON_SHORT_NAME); ?></title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: #aaa;
      min-height: 100vh;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 20px;
      padding-top: 25vh;
      color: #333;
    }
    main {
      background: white;
      border-radius: 12px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      max-width: 600px;
      width: 100%;
      padding: 40px;
    }
    h1 {
      color: #10354c;
      font-size: 28px;
      margin-bottom: 16px;
      font-weight: 600;
    }
    p {
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 16px;
    }
    .error-code {
      background: #f7f7f7;
      border: 1px solid #e0e0e0;
      border-radius: 6px;
      padding: 12px 16px;
      margin: 24px 0;
      font-family: 'Courier New', monospace;
      font-size: 14px;
      color: #666;
    }
    label {
      display: block;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #999;
      margin-bottom: 4px;
    }
    output {
      display: block;
      font-size: 18px;
      font-weight: bold;
      color: #333;
      user-select: all;
    }
    a {
      color: #10354c;
    }
    a.button {
      display: inline-block;
      background: #10354c;
      color: white;
      text-decoration: none;
      padding: 14px 32px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 16px;
      box-shadow: 0 4px 12px rgba(16, 53, 76, 0.4);
    }
    a.button:hover {
      box-shadow: 0 6px 16px rgba(16, 53, 76, 0.5);
    }
    footer {
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1px solid #e0e0e0;
      color: #999;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <main>
    <h1>Something went wrong</h1>
    <p>
      We're sorry, but an unexpected error occurred.
    </p>
    <p>
      Try refreshing the page or come back later. If the problem persists, please contact our support team at <a href="<?php echo htmlspecialchars($mailto); ?>"><?php echo htmlspecialchars(SUPPORT_EMAIL); ?></a>, quoting the code below.
    </p>
    <div class="error-code">
      <label for="error-code">Error reference code</label>
      <output id="error-code"><?php echo htmlspecialchars($err_code); ?></output>
    </div>
    <a class="button" href="<?php echo htmlspecialchars($mailto); ?>">
      Contact support
    </a>
    <footer>
      <?php echo htmlspecialchars(CON_SHORT_NAME); ?> Member Portal
    </footer>
  </main>
</body>
</html>
