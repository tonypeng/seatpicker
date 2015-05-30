<?php
class CreateStudentsController extends SiteController {
    public function getPageTitle() {
        return 'Graduation: Sign in';
    }

    public function renderPage() {
        $row = 1;
        if (($handle = fopen(__DIR__.'/tony.data.ID.name.password.csv', "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                echo "<p> $num fields in line $row: <br /></p>\n";
                $row++;
                $student_id = $data[0];
                $first_name = $data[1];
                $last_name = $data[2];
                $gender = $data[3] - 1;
                $password = $data[4];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = db()->prepare('INSERT INTO `students` (first_name, last_name, gender, student_id, password)
                    VALUES (:fn, :ln, :g, :sid, :p)');
                $stmt->execute(array(
                    ':fn' => $first_name,
                    ':ln' => $last_name,
                    ':g' => $gender,
                    ':sid' => $student_id,
                    ':p' => $hashed_password
                ));
                echo $student_id.' '.$first_name.' '.$last_name.' '.$gender.' '.$password;
            }
            fclose($handle);
        }

        return 'done';
    }
}