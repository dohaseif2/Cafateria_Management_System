<?php
// Include DB class
require_once "../models/db.php";
require_once "../models/allProducts&usersModel.php";

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        //Initialize an array to store errors
        $errors = [];

        // Get the form data
        $name = $_POST["name"];
        $email = $_POST["email"];
        $roomNum = $_POST["roomNum"];
        $ext = $_POST["ext"];
        $userId = $_POST["userId"];

        // Instantiate the DB class
        $db = new DB();
        $db2 = new allup();

        // Check if the room exists in the database
        $roomExists = $db2->exists("room", ["id" => $roomNum]);

        if (!$roomExists) {
            // Room doesn't exist, return an error response
            $errors[] = "Room not found. Please select a valid room.";
        }
        // Check if the extension exists in the database
        $extExists = $db2->exists("room", ["ext" => $ext]);
        if (!$extExists) {
            // Extension doesn't exist, return an error response
            $errors[] = "Extension not found. Please select a valid extension.";
        }

        // Check if the room assigned to the user already has the given extension
        $roomWithExtExists = $db2->exists("room", ["id" => $roomNum, "ext" => $ext]);
        if (!$roomWithExtExists) {
            // Extension is not assigned to the room, return an error response
            $errors[] = "The room assigned to the user doesn't have the provided extension.";
        }

        // Check if the email already exists in the database
        $currentUserEmail = $db2->select1(
            "SELECT email FROM user WHERE id = :id",
            [":id" => $userId]
        )[0]['email'];

        if ($email !== $currentUserEmail) {
            // If the email is different from the current user's email, check if it exists
            $emailExists = $db2->exists("user", ["email" => $email]);
            if ($emailExists) {
                // Email already exists, return an error response
                //                 $errors[] = "Email already exists. Please choose a different email.";
            }
        }

        // Check if a file is uploaded
        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
            // File upload is successful and within the allowed size and format
            $tempFile = $_FILES['profilePicture']['tmp_name'];
            $targetPath = '../public/images/';

            // Get file information
            $fileName = $_FILES['profilePicture']['name'];
            $fileSize = $_FILES['profilePicture']['size'];
            $fileType = $_FILES['profilePicture']['type'];

            // Check file size (in bytes)
            if ($fileSize > 6 * 1024 * 1024) { // 6 MB (6 * 1024 * 1024 bytes)
                // File size exceeds the limit, add an error to the array
                $errors[] = "The uploaded file exceeds the maximum file size limit of 6 MB.";
            }

            // Check file type
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            if (!in_array($fileType, $allowedTypes)) {
                // File type is not allowed, add an error to the array
                $errors[] = "Only PNG, JPEG, and JPG file formats are allowed.";
            }

            // Move the uploaded file to the target directory
            $newFileName = uniqid() . '_' . $fileName; // Generate unique file name to avoid conflicts
            $targetFile = $targetPath . $newFileName;
            if (!move_uploaded_file($tempFile, $targetFile)) {
                // File upload failed, add an error to the array
                $errors[] = "Failed to upload the file.";
            }
        }
        //If there are errors, throw an exception with the errors array
        if (!empty($errors)) {
            throw new Exception("Validation failed. " . implode("\n", $errors));
        }

        // Update user data in the database
        $updateData["name"] = $name;
        $updateData["email"] = $email;
        $updateData["room_id"] = $roomNum;
        $db->update("user", ["id" => $userId], $updateData);


        // Update the extension in the room table
        $db->update("room", ["id" => $roomNum], ["ext" => $ext]);

        // Fetch the updated user data from the database (from both tables)
        $updatedUser = $db2->select1(
            "SELECT u.*, r.ext FROM user u INNER JOIN room r ON u.room_id = r.id WHERE u.id = :id",
            [":id" => $userId]
        );

        // Prepare JSON response
        $response = [
            'success' => true,
            'message' => 'User updated successfully',
            'updatedUser' => $updatedUser // Return updated user data for displaying changes
        ];

        // Return the success flag and updated user data in JSON format
        echo json_encode($response);
        exit(); // No need to redirect or output anything else
    } catch (Exception $e) {
        // Error occurred, handle it
        $response = [
            'success' => false,
            'message' => $e->getMessage(),
        ];

        // Return the error message in JSON format
        echo json_encode($response);
        exit();
    }
}

// If the script reaches this point, it's not a POST request or there was an error during processing
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit();
