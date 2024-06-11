<?php 
$con = mysqli_connect("localhost", "root", "", "questions");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $question = mysqli_real_escape_string($con, $_POST['question']);
            $choice1 = mysqli_real_escape_string($con, $_POST['choice1']);
            $choice2 = mysqli_real_escape_string($con, $_POST['choice2']);
            $choice3 = mysqli_real_escape_string($con, $_POST['choice3']);
            $choice4 = mysqli_real_escape_string($con, $_POST['choice4']);
            $correctAnswer = mysqli_real_escape_string($con, $_POST['rightanswer']);

            $sql = "INSERT INTO assessment (Question, choice1, choice2, choice3, choice4, rightanswer) VALUES ('$question', '$choice1', '$choice2', '$choice3', '$choice4', '$correctAnswer')";

            if (mysqli_query($con, $sql)) {
                $newQuestionId = mysqli_insert_id($con);
                $result = mysqli_query($con, "SELECT * FROM assessment WHERE id = $newQuestionId");
                $newQuestion = mysqli_fetch_assoc($result);
                echo json_encode(['status' => 'success', 'message' => 'New question added successfully', 'question' => $newQuestion]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . mysqli_error($con)]);
            }
        } elseif ($_POST['action'] == 'delete') {
            $id = mysqli_real_escape_string($con, $_POST['id']);
            $sql = "DELETE FROM assessment WHERE id = $id";

            if (mysqli_query($con, $sql)) {
                echo json_encode(['status' => 'success', 'message' => 'Question deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . mysqli_error($con)]);
            }
        } elseif ($_POST['action'] == 'delete_all') {
            $sql = "DELETE FROM assessment";

            if (mysqli_query($con, $sql)) {
                echo json_encode(['status' => 'success', 'message' => 'All questions deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . mysqli_error($con)]);
            }
        }
        exit;
    }
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Add your existing styles here */
        .delete-btn {
            background-color: #ff5c5c;
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        .delete-btn:hover {
            background-color: #ff1a1a;
        }.delete-all-btn {
            background-color: #ff5c5c;
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }  * {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
        }
        .container {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100vh;
            border: 2px solid black;
        }
        .header {
            text-align: center;
            background-color: #577F5B;
            color: white;
            padding: 20px;
        }
        .content {
            display: flex;
            flex-direction: row;
            flex: 1;
            overflow: hidden;
        }
        .box1, .box2 {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: linear-gradient(#FFE08F, #DEC4AC);
        }
        .result {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .result-info {
            font-size: 20px;
            font-weight: bold;
            line-height: 1.6;
        }
        .upload-btn {
            font-size: 20px;
            font-weight: bold;
            border-radius: 20px;
            border: none;
            padding: 10px;
            background-color: #E7E7E7;
            cursor: pointer;
            margin-top: 20px;
        }
        .upload-btn:hover {
            background-color: #CFCFCF;
        }
        .addbtn {
            margin: 20px 0;
            background-color: #577F5B;
            padding: 10px;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }
        .addbtn:hover {
            background-color: #2C3E50;
        }
        .form-container {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 2px solid #577F5B;
            border-radius: 10px;
            z-index: 1;
            width: 80%;
            max-width: 500px;
        }
        .form-container input, .form-container select, .form-container textarea {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 16px;
        }
        .form-container button {
            background-color: #577F5B;
            padding: 10px;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .form-container button:hover {
            background-color: #2C3E50;
        }
        .closebtn {
            background-color: #ff5c5c;
            padding: 5px;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .closebtn:hover {
            background-color: #ff1a1a;
        }
        .question-box {
            background-color: white;
            padding: 20px;
            border: 2px solid #577F5B;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        #questions-list {
            background-color: white;
            border-radius: 10px;
            width: 100%;
            margin-top: 20px;
            flex: 1;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            .form-container {
                width: 90%;
            }
            .header {
                font-size: 24px;
            }
            .upload-btn, .addbtn {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Assessment Management</h1>
        </div>
        <div class="content">
            <div class="box1">
                <h2 style="font-size: 32px; font-weight: bold; margin-bottom: 20px;">Assessment Details</h2>
                <div class="result">
                    <div class="result-info">
                        <p>Course Name: Aquaphonics Full Guide</p>
                        <p>Assessment Duration: 60 mins</p>
                        <p>Total number of questions: 60</p>
                        <p>Passing Score: 75%</p>
                        <button class="upload-btn">Upload Assessment</button>
                    </div>
                </div>
            </div>
            <div class="box2">
                <button class="addbtn">Add question</button>
                <button class="delete-all-btn">Delete All Questions</button>
                <div id="questions-list">
                    <?php
                    $result = mysqli_query($con, "SELECT * FROM assessment");
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='question-box' data-id='" . $row['id'] . "'>";
                            echo "<p><strong>Question:</strong> " . $row['Question'] . "</p>";
                            echo "<p><strong>Choice 1:</strong> " . $row['choice1'] . "</p>";
                            echo "<p><strong>Choice 2:</strong> " . $row['choice2'] . "</p>";
                            echo "<p><strong>Choice 3:</strong> " . $row['choice3'] . "</p>";
                            echo "<p><strong>Choice 4:</strong> " . $row['choice4'] . "</p>";
                            echo "<p><strong>Correct Answer:</strong> " . $row['rightanswer'] . "</p>";
                            echo "<button class='delete-btn'>Delete</button>";
                            echo "</div>";
                        }
                    } else {
                        echo "No questions found.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-container">
        <button class="closebtn">&times;</button>
        <h3>Add a new question</h3>
        <form id="questionForm">
            <input type="hidden" name="action" value="add">
            <label for="question">Question:</label>
            <textarea id="question" name="question" required></textarea>
            
            <label for="choice1">Choice 1:</label>
            <input type="text" id="choice1" name="choice1" required>
            
            <label for="choice2">Choice 2:</label>
            <input type="text" id="choice2" name="choice2" required>
            
            <label for="choice3">Choice 3:</label>
            <input type="text" id="choice3" name="choice3" required>
            
            <label for="choice4">Choice 4:</label>
            <input type="text" id="choice4" name="choice4" required>
            
            <label for="rightanswer">Correct Answer:</label>
            <select id="rightanswer" name="rightanswer" required>
                <option value="choice1">Choice 1</option>
                <option value="choice2">Choice 2</option>
                <option value="choice3">Choice 3</option>
                <option value="choice4">Choice 4</option>
            </select>
            
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        document.querySelector('.addbtn').addEventListener('click', function() {
            document.querySelector('.form-container').style.display = 'block';
        });

        document.querySelector('.closebtn').addEventListener('click', function() {
            document.querySelector('.form-container').style.display = 'none';
        });

        document.getElementById('questionForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form from submitting the default way
            var form = event.target;
            var formData = new FormData(form);
            formData.append('action', 'add');

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Your work has been saved",
                            showConfirmButton: false,
                            timer: 1500
                            });
                    // Create new question box
                    var questionBox = document.createElement('div');
                    questionBox.className = 'question-box';
                    questionBox.setAttribute('data-id', data.question.id);
                    questionBox.innerHTML = `
                        <p><strong>Question:</strong> ${data.question.Question}</p>
                        <p><strong>Choice 1:</strong> ${data.question.choice1}</p>
                        <p><strong>Choice 2:</strong> ${data.question.choice2}</p>
                        <p><strong>Choice 3:</strong> ${data.question.choice3}</p>
                        <p><strong>Choice 4:</strong> ${data.question.choice4}</p>
                        <p><strong>Correct Answer:</strong> ${data.question.rightanswer}</p>
                        <button class='delete-btn'>Delete</button>
                    `;
                    // Append new question box to questions list
                    document.getElementById('questions-list').appendChild(questionBox);
                    // Hide the form
                    document.querySelector('.form-container').style.display = 'none';
                    // Clear the form
                    form.reset();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
        // Add event listener to the parent element that contains all the delete buttons
        document.getElementById('questions-list').addEventListener('click', function(event) {
    // Check if the clicked element is a delete button
    if (event.target.classList.contains('delete-btn')) {
        var deleteButton = event.target;
        var questionId = deleteButton.parentElement.getAttribute('data-id');

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=delete&id=' + questionId,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: "Deleted!",
                            text: data.message,
                            icon: "success"
                        });
                        deleteButton.parentElement.remove(); // Remove the question box from the DOM
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message,
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "An error occurred while deleting the question.",
                        icon: "error"
                    });
                });
            }
        });
    }
});

document.querySelector('.delete-all-btn').addEventListener('click', function() {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=delete_all',
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: "Deleted!",
                        text: "All questions have been deleted.",
                        icon: "success"
                    });
                    document.getElementById('questions-list').innerHTML = ''; // Remove all questions from the DOM
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: data.message,
                        icon: "error"
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: "Error!",
                    text: "An error occurred while deleting questions.",
                    icon: "error"
                });
            });
        }
    });
});

      
    </script>
</body>
</html>


