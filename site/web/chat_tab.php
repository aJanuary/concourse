<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Chat tab</title>
  <style>
    body {
      font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      text-align: center;
      font-size: 16px;
      max-width: 400px;
      margin: 0 auto 1rem auto;
    }

    section {
      margin: 1.5rem 0 0 0;
    }

    section+section {
      margin: 5rem 0 0 0;
    }

    h1, h2 {
      color: rgb(44, 50, 66);
      font-size: 1.125rem;
      font-weight: 500;
      margin: 1.5rem 0 2px 0;
    }

    p, li {
      color: rgb(79, 84, 97);
      font-size: 0.875rem;
      line-height: 1.5rem;
    }

    p {
      margin: 1rem 1rem 0 1rem;
    }

    li {
      text-align: left;
    }

    ol {
      display: inline-block;
    }

    .bttn {
      display: inline-block;
      text-decoration: none;
      color: white;
      font-family: ReplaceEmoji, -apple-system, BlinkMacSystemFont, Segoe UI, sans-serif;
      font-size: .875rem;
      font-weight: 600;
      min-height: 3rem;
      line-height: 3rem;
      margin: 2rem 0.75rem 0 0.75rem;
      max-width: calc(100vw - 1.5rem);
      background-color: #1d6eaa;
      border-radius: 0.25rem;
      padding: 0 2rem;
    }

    .bttn:hover {
      background-color: #215c8b;
    }

    .nobreak {
      word-break: keep-all;
    }

    #pip {
      display: none;
    }
  </style>
</head>

<body>
  <section>
    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="none">
      <circle cx="32" cy="32" r="32" fill="#f1f2f5"></circle>
      <path fill-rule="evenodd" clip-rule="evenodd"
        d="M39.0164 30.9857C39.0164 33.1917 37.2239 34.98 35.0127 34.98H26.0045C25.2024 34.9648 24.4274 35.2704 23.8526 35.8288L21.8508 37.826C21.759 37.921 21.6327 37.975 21.5005 37.9757C21.2241 37.9757 21 37.7522 21 37.4765V24.9943C21 22.7883 22.7925 21 25.0036 21H35.0127C37.2239 21 39.0164 22.7883 39.0164 24.9943V30.9857ZM41.0182 32.9829V24.9943C42.1159 25.0052 43.0001 25.8962 43 26.9914V41.5007C43 41.7765 42.7759 42 42.4995 42C42.3673 41.9992 42.241 41.9452 42.1492 41.8502L40.1474 39.8531C39.5824 39.2886 38.815 38.9724 38.0155 38.9743H27.0055C25.8999 38.9743 25.0036 38.0802 25.0036 36.9772H37.0146C39.2257 36.9772 41.0182 35.1889 41.0182 32.9829Z"
        fill="#757a8a"></path>
    </svg>
    <h1>Start chatting</h1>
    <p>Join in with the chat over in the convention Discord.</p>
    <p><a href="/deep-link/chat?room_id=<?php echo $_GET['room_id']; ?>" target="_blank" id="forumBttn" class="bttn">Chat about this item</a></p>
  </section>
  <section id="pip">
    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="none">
      <circle cx="32" cy="32" r="32" fill="#f1f2f5"></circle>
      <path d="M30 37 L21 37 L21 24 L42 24 L42 31" style="fill:none; stroke:#757a8a; stroke-width:2"></path>
      <rect x="32" y="34" width="13" height="9" fill="#757a8a"></rect>
    </svg>
    <h2>Picture-in-picture</h2>
    <p>To watch the item and chat at the same time, you can use the picture-in-picture feature in the video player.</p>
    <ol>
      <li id="inst-hover">Hover over/tap the video player</li>
      <li id="inst-click">Click the picture-in-picture button</li>
    </ol>
    <img alt="A screenshot of the video controls with the picture-in-picture icon circled" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMMAAAA1CAYAAAD8tF+uAAABhGlDQ1BJQ0MgcHJvZmlsZQAAKJF9kT1Iw0AcxV9Ti0UqonYQdchQneyiIo61CkWoEGqFVh1MLv2CJg1Jiouj4Fpw8GOx6uDirKuDqyAIfoA4OzgpukiJ/0sKLWI9OO7Hu3uPu3eAUC8zzeqKAZpum6lEXMxkV8XuVwQwgCBG0C8zy5iTpCQ6jq97+Ph6F+VZnc/9OXrVnMUAn0gcY4ZpE28Qz2zaBud94jAryirxOfGESRckfuS64vEb54LLAs8Mm+nUPHGYWCy0sdLGrGhqxNPEEVXTKV/IeKxy3uKslauseU/+wlBOX1nmOs1RJLCIJUgQoaCKEsqwEaVVJ8VCivbjHfzDrl8il0KuEhg5FlCBBtn1g//B726t/NSklxSKA4EXx/kYA7p3gUbNcb6PHadxAvifgSu95a/UgdlP0mstLXIE9G0DF9ctTdkDLneAoSdDNmVX8tMU8nng/Yy+KQsM3gI9a15vzX2cPgBp6ip5AxwcAuMFyl7v8O5ge2//nmn29wNZNHKcLHim7QAAAAZiS0dEAAEAAwAAxmUslgAAAAlwSFlzAAAuIwAALiMBeKU/dgAAAAd0SU1FB+gFEgg1DziurbQAAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAALTElEQVR42u2df1CU1RrHP++uCLijNAkBM6IEGmCFccWByRLtekFxugZONV2nyMBQkkwQS+Y2/WGNDVhZpI0TOdlM1+lOaTUSEFlo43QdU7O5ijuNQMoVTSWWBV1YeM/9w2H19V1+7b6LiOc7c2Z2z3n2vM/uOd/zPM/5tYpiRiAhIYFJ/gQSEpIMEhKSDBISkgwSEv1gjK8fkEAi/xBZRBHNPHU+AoGKSog5UP76EiMKitGzSaWijCw1G3/8UVD6ld1m2kKRku96n0Mea9R1AFzkAimmWbKFJG49y3BcbeQuEYo//h7XESPiiBCTAYhgMm09KheVC3yq7OBVZZ1sLYmRHTOUijJaerqIEJO9IkJfCBYhrFbXsk89JFtLYuRahuNqo2skdwcVlfPKOWJMkwZVn1Wp44xy2m2dCWImbT0qZ5TTvGMqoZytsvUkbk7MIHquvd5EGbm84FbOiZNzNDOdSK8Uq+ccIdzltuwiF7ibUNl6EoPr5GYfukl9EWEbW5jPbK+JABBFGAWs4gyn9a4TIdhRWU6ebGkJQ1kjBpPgarKj6tIJGl3lvkr7OaR7bgPnxXLyfP5smW7tNNg+PiQ3yY6qyx8/jOt2J2gkAm08cYbThlgiCekmDaonJ5B404kAMJ1IGqjX5EUwmdcpkS0uMTwBdKkoI1d9QUeENWvWcM899/hUwT179lBRUaHJ288hEph5U4kpMfosw6BihgblvMZX/5oaAYgffvhB+BqvvfaaWz/wZsQtMo3umGFQ6wzBIsT1WkXl7/xNU75hwwZOnTplKJsXLVrE448/3mf5u2xiNWs17tImylhLvhwKDcCUKVP47LPPMJuvDqtCCFasWMGRI0dG7XcekAxWtUnz/n806WQqKio4ePCgoYqFhYX1S4Z/so5MntAE1Kks1MlNmzaNl156ibi4OEymoblSqqpSV1fHe++9h9Vqva3IMHPmTJKSkjR5s2fPvr3JECrCdEHsSMF0IjWB/d1EacqTk5Opra3F39/zbSLz5s0jOzub2bNnc/jwYa91XrJkCZs3byYsLMyjz1+6dIni4mK2b99ubEcYM4bu7m7NIOJuYOnvM0bCz8+PO++8U5PX0tKC0+kEwGw2ExwcrCm32Ww4HA6vgot+/ajr/XIbPRpfrDdmSEpKMtzPe/nll/uNGXrTCRr7jB0OHDhgWOxSU1NjyPeqr6/3WhebzSbMZrNhv3V4eLhobW0V1dXVIjMzUxQVFYm2tjbdcx0Oh3jjjTdEenq62LVrl+jo6BBxcXE+8fNTUlJ0z09JSXGVx8TE6MqffPJJ38cMvejGOeJM2zuU8Dbvu97fdd02jRkzZujk29vbsVqt3HvvvQQEBGjK7HY7VquV+++/X2dNEhMTDdE3JCTE6zomTJhAQEAAHR0dhui0ceNGgoKCSE1NJTU1tU85f39/iouLNXmbN28mLS3NZ+1bX1/PiRMnXFaxFx0dHezZsweA6Oho4uLiDJl2GrRl+A/HRpxlAMSfdGn07M2/EWVlZWLMmDECEIGBgeKDDz5wlb311luu0dZisYjy8nLd5434Xna73RBLZbFYDNEnMTFRqKrqlS6PPvqozyxDaWnpgLKFhYWGWIYhRZSqm4W3kYAOBh4h9+7dS35+PqtXr8Zut7N+/XpWrlzJL7/8QmVlJYWFhRQVFdHe3s6aNWtYvnw5dXV1oz5Qzs7ORlH0h7B6enrYsGEDU6dOJTIykqKioj798ZycnFGzIHHLW4avqRnQMuTl5WlG5ra2NlFbWyvOnj0rnnvuOaEoinA4HEIIIVpbW0Vtba1obm4e9ZbBYrGInTt36uovKCjQyS5dulQnV1VVJSZOnGh4+4eHh4vc3FyRnJw8oOyMGTNEbm6uiI6O9soyDIkMR7EOGxmmTZsmFi9eLGJiYgaU3UTZgGRYsWJFn51x2bJlwmQyic7Ozn474GgkQ2+qrq521e10OkVAQIC+UymK+OOPP1xyR48eFSaTadQsug3JTYrk7mGzWL/99htfffXVoOb3/8WOAWWSk5MB+PDDD+nq6qK4uBhFUfjiiy9ITk5GVVU++ugjHA4HhYWFKIriCtCMRlNTE4mJia60c+dOAHJzczX57tKxY8d842peF4xfuXLFrUskhMBms7neX758GVVVGS0Y0mySGbPXD/z4448JDR36wRxVVVm0aNGgZN2tRGdlZbF7924KCgooKCgAYO7cuSxevBiTycSXX35JXl4eeXlXz0ikpqaSnp7ukx/d4XBo1izOnz9/dYHTah1wLcNutxuuzyOPPMLDDz/sej9+/HiSkpJ0C6lRUVFERV1by4mPjyczM5Ndu3bdHjGDjW63LoinblJDQ4NHboHT6Ry0m7SJMgEIm82mq6eyslKUlpaKmpoaXVlFRYUoKSkR3333na6stbV1VLpJOTk5oru7W1d/XV2diIyMdMmFhoaKgwcPutXllVdeGRWzSQNahhblkmZv0kjEEp5wm//999/z2GOPafIWLFjAggUL3Mqnp6f3aQ1qamoM07erq8s1d94XAgICiI2NpbW1lcbGRs0qsMViMUyXcePGufYfXY/Y2FisViv79++nu7ubOXPmMG7cOLd1jB8//vZwkz5VdrBaXNsQt59DzEF7n1FZWZnGl3SHrKwszp4965MvEcQd1/xacLlI+fn5xMfHa0y7Jzh58iQvvviiYfqePn2ahISEfmViYmI4efIkVVVVPPXUU678H3/8kYceesgwXbZu3crKlSuJjY3VlY0dO5b58+f3+/nm5mY2btzosw4aHR3tco9/+uknWlpaALBYLMydOxeA6dOnDw8ZXlXWaXaHJjCTv5DIEX525c2aNfBlX4GBvrtBzw8/1+vrV8mbmpqIj4/n+eefJz09HT8/vyHV63Q62b17N+Xl5XR1dRmiq9PpJCgoiNzc3H7levcuTZ06VSMbHh6OEIKenh5D9Onu7qagoIBvvvkGIQR79+4lIiKCmJgYt/Jnzpzh2LFjLFy4ELPZTHFxMe3t7T5r24yMDDIyMlwx3r59+wCYNGmS4RMcgzrcc+OVMNvYwlryue+++wgKChrUgw4fPozD4aChoYHIyEiPGs1dZ77xpo6RfnPGJ598wtNPP+1VHZWVlYYH98888wwHDhzg1KlT+Pn5sWPHDo1FAvj222/JyMjg8uXLTJo0ibS0NLZv387VWWdjMWXKFJ599lnd5Mvvv/8OwMSJE1m1apWm/PPPP+f48ePu4mLjyJBDHm/3vK/1Ez08WWY0GRo4TzAhOqKOVPj5+ZGWlubRjBpc3Z9TXV3NlStXfKrnsmXLdDtj169fz5tvvnkrThIZM5vUm27c/+PpyTIjZ5OWk6c78YY82WVIevDBB3VtkJGRIW/HACjt0V8c5ol1MNIyXOSK5kpLeVOGkf6zwgMPPOA6ECWE4Ndff/XZ+YVbyjLg5uzA9XP6w20ZXqdEWgWZbt69SeD93UlxcXGMHTvWI4ZfvxXhRj06aCeMCXJIlxgey4Cb1V5PLYSnaTl54gKXpVWQ6eZbhr5GZm9mmIaCG+MEgBqqyCRdDoESXlkGj8nQFyF8SQp3z+ukk2DkX2JJeE8Gr3ptX53ejsoJGg27JftratwSoYF61lMoW1vCoBk0LyxDb0edx1/7/aynC2HnaCOQcZjckE5Oo0rcNDdpILi7j9UdVFTXP3520kmd8l9+Vg5hwkSWms1Y/DEN8Y8RJSSG1TIMFvvUQySImT5TeIJZXjAs4RsY3rNSTLOYYDaxzbTFsDpVVJqVs5IIEreWZXCHDaKEpSKLIHGHy/83Ye7TGVIR2JQ/+beyU7pDEqOLDBISt6WbJCEhySAhIckgISHJICEhySAhMRrxf6iDOtHaOCnmAAAAAElFTkSuQmCC" />
  </section>

  <script>
    if (document.pictureInPictureEnabled) {
      document.getElementById('pip').style.display = 'block';
    }

    if (matchMedia('(pointer:fine)').matches) {
      document.getElementById('inst-hover').textContent = 'Hover over the video player';
      document.getElementById('inst-click').textContent = 'Click the picture-in-picture button';
    } else if ((('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0))) {
      document.getElementById('inst-hover').textContent = 'Tap the video player';
      document.getElementById('inst-click').textContent = 'Tap the picture-in-picture button';
    }
  </script>
</body>

</html>