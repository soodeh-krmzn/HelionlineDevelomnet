<!DOCTYPE html>
<html>
<head>
  <title>Div Filling Example</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    #container {
      width: 100%;
      height: 100px;
      background-color: #f1f1f1;
      position: relative;
      overflow: hidden;
    }

    #progress-bar {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      background-color: #4CAF50;
      width: 50%;
      transition: width 20s linear;
    }
  </style>
</head>
<body>
  <div id="container">
    <div id="progress-bar"></div>
  </div>

  <script>
    $(document).ready(function() {
      // Total time in minutes
      var totalTime = 20;

      // Time already passed in minutes
      var timePassed = 10;

      // Update the progress bar width
      var progressWidth = (timePassed / totalTime) * 100;
      $("#progress-bar").css("width", progressWidth + "%");

      // Start the timer
      var interval = setInterval(function() {
        // Decrement the remaining time
        timePassed++;

        // Update the progress bar width
        progressWidth = (timePassed / totalTime) * 100;
        $("#progress-bar").css("width", progressWidth + "%");

        // Check if the timer has reached the end
        if (timePassed >= totalTime) {
          clearInterval(interval);
          console.log("Time's up!");
        }
      }, 60000); // Update every minute
    });
  </script>
</body>
</html>
