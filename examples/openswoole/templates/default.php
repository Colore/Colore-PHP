<!DOCTYPE html>

<html lang="en" xml:lang="en">

<head>
    <meta charset="ISO-8859-1">
    <title>Colore Example / <?= $template['page_title'] ?></title>
    <script language="javascript">
        function doPing() {
            fetch(`/api/ping?message=${document.getElementById('pingText').value}`)
            .then((result) => result.json())
            .then((data) => document.getElementById('pingResult').value = data.message)
        }

        function getJoke(jokeId) {
            let jokeUri = `/api/joke/random`

            if(jokeId != null) {
                jokeUri = `/api/joke?id=${jokeId}`
            }

            fetch(jokeUri)
            .then((result) => result.json())
            .then((data) => document.getElementById('jokeResult').value = data.joke)
        }

        function getTrunk() {
            fetch(`/api/trunk?message=${document.getElementById('pingText').value}`)
            .then((result) => result.json())
            .then((data) => {
                document.getElementById('jokeResult').value = data.joke
                document.getElementById('pingResult').value = data.message
            })
        }
    </script>
</head>

<body>
    <p><?= $template['place_holder_message'] ?></p>

    <p>Ping: <input type="text" id="pingText" value="test" size="64" length="64" /></p>

    <p>Result: <input type="text" disabled id="pingResult" size="128" length="128" /></p>
    <p>Result: <input type="text" disabled id="jokeResult" size="128" length="128" /></p>

    <p>
        <button onclick="doPing();">Ping</button>
        <button onclick="getJoke();">Get Random Joke</button>
        <button onclick="getJoke(0);">Get Joke 1</button>
        <button onclick="getJoke(1);">Get Joke 2</button>
        <button onclick="getJoke(2);">Get Joke 3</button>
        <button onclick="getJoke(3);">Get Joke 4</button>
        <button onclick="getJoke(4);">Get Joke 5</button>
    </p>
    <p>
        <button onclick="getTrunk();">Get Trunk</button>
    </p>
</body>

</html>
