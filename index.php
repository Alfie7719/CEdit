<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PyEdit</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #000;
        }
        #editor {
            position: absolute;
            top: 50px;
            width: 100%;
            height: calc(100% - 50px);
        }
        #run-button {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            background-color: purple;
            color: #fff;
            border: none;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
        }
        #output {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 150px;
            background-color: #f0f0f0;
            overflow-y: scroll;
            padding: 10px;
        }
        #pyedit-title {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            font-weight: bold;
            color: orange;
        }
    </style>
</head>
<body>
    <div id="pyedit-title">PyEdit</div>
    <button id="run-button" onclick="runCode()">Run</button>
    <div id="editor"></div>
    <div id="output"><?php if(isset($_POST['code'])) { 
        $code = $_POST['code'];
        $output = '';
        $file = 'temp.py';
        file_put_contents($file, $code);

        exec("python $file 2>&1", $output);
        
        unlink($file);

        foreach ($output as $line) {
            echo htmlspecialchars($line) . "<br>";
        }
    } ?></div>

    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.js"></script>
    <script>
        require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs' }});
        require(['vs/editor/editor.main'], function() {
            var editor = monaco.editor.create(document.getElementById('editor'), {
                value: [
                    '# Write your Python code here'
                ].join('\n'),
                language: 'python',
                theme: 'vs-light',
                automaticLayout: true
            });
            window.editor = editor;
        });

        function runCode() {
            var code = window.editor.getValue();
            // Send code to PHP script for execution
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("output").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("POST", "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("code=" + encodeURIComponent(code));
        }
    </script>
</body>
</html>
