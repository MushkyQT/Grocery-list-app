<?php

session_start();

require_once('creds.php');

if (mysqli_connect_error()) {
    die("Connection to database failed.<br>");
}

$tableContent = "";
$fatal = "";
$signUp = false;
$loggedIn = false;

if (isset($_POST['logOut'])) {
    session_unset();
}

if (isset($_POST['newUser']) && isset($_POST['newPass']) && isset($_POST['newPassConfirm'])) {
    $newUser = $_POST['newUser'];
    $newPass = $_POST['newPass'];
    $newPassConfirm = $_POST['newPassConfirm'];

    // I.E. pas de caracteres speciaux dans l'username (patri$$ck) et longueur min/max
    // I.E. mot de passe assez secure (au moins une maj, un caractere special, un chiffre, une longueur min/max)
    if ($newUser != "" && $newPass != "" && $newPassConfirm != "") {
        if ($newPass == $newPassConfirm) {
            $availabilityCheck = "SELECT * FROM `utilisateurs` WHERE `nom` = '" . $newUser . "'";
            $myResult = mysqli_query($myConnection, $availabilityCheck);
            if (mysqli_num_rows($myResult) != 0) {
                $fatal = "Your chosen username has already been taken. Please try something else.";
            } else {
                $addUserRequest = "INSERT INTO `utilisateurs` (`nom`, `motDePasse`) VALUES ('" . $newUser . "', '" . $newPass . "')";
                if ($myResult = mysqli_query($myConnection, $addUserRequest)) {
                    $fatal = "Created account for " . $newUser . ".";
                    $signedUp = true;
                    $_SESSION['signedUp'] = true;
                } else {
                    $fatal = "Account creation fail.";
                }
            }
        } else {
            $fatal = "Your passwords do not match, please try again carefully.";
        }
    } else {
        $fatal = "Please submit a value for all three fields.";
    }
}

if (isset($_POST['signUp'])) {
    $signUp = true;
} else {
    $signUp = false;
}

if (isset($_SESSION['signedUp'])) {
    $signedUp = true;
    $signUp = false;
} else {
    $signedUp = false;
}

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    $_POST['username'] = $_SESSION['username'];
    $_POST['password'] = $_SESSION['password'];
}

if (isset($_POST['username']) && isset($_POST['password']) && !isset($_POST['signUp'])) {
    $usernameSubmitted = $_POST['username'];
    $passwordSubmitted = $_POST['password'];

    if ($usernameSubmitted != "" && $passwordSubmitted != "") {
        $myRequest = "SELECT * FROM `utilisateurs` WHERE `utilisateurs`.`nom` = '" . $usernameSubmitted . "'";
        if ($currentResult = mysqli_query($myConnection, $myRequest)) {
            $myResult = mysqli_fetch_array($currentResult);
            if ($myResult) {
                $leBonMotDePasse = $myResult['motDePasse'];
                if ($passwordSubmitted == $leBonMotDePasse) {
                    $loggedIn = true;
                    $_SESSION['username'] = $usernameSubmitted;
                    $_SESSION['password'] = $passwordSubmitted;
                } else {
                    $fatal = "Wrong password for " . $usernameSubmitted . ".<br>";
                }
            } else {
                $fatal = "User does not exist.<br>";
            }
        } else {
            $fatal = "Request failed.<br>";
        }
    } else {
        $fatal = "Both fields required to log-in.<br>";
    }
}


if ($_POST && isset($_POST['purchased'])) {
    $myRequest = "UPDATE `groceries` SET `purchased` = !`purchased` WHERE `id` = " . $_POST['purchased'];
    if ($myResult = mysqli_query($myConnection, $myRequest)) {
        $fatal = "Purchased update successful.";
    } else {
        $fatal = "Purchased update fail.";
    }
} elseif ($_POST && isset($_POST['del'])) {
    $myRequest = "DELETE FROM `groceries` WHERE `id` = " . $_POST['del'];
    if ($myResult = mysqli_query($myConnection, $myRequest)) {
        $fatal = "Deletion successful.";
    } else {
        $fatal = "Deletion fail.";
    }
} elseif ($_POST && isset($_POST['addProduct'])) {
    if ($_POST['addProduct'] != "") {
        $myRequest = "INSERT INTO `groceries` (`product`, `purchased`) VALUES ('" . $_POST['addProduct'] . "', 0)";
        if ($myResult = mysqli_query($myConnection, $myRequest)) {
            $fatal = "Added " . $_POST['addProduct'] . " to the grocery list.";
        } else {
            $fatal = "Addition fail.";
        }
    } else {
        $fatal = "Please submit a non-empty value.";
    }
} elseif ($_POST && isset($_POST['editProduct']) && isset($_POST['modify'])) {
    if ($_POST['editProduct'] != "") {
        $myRequest = "UPDATE `groceries` SET `product` = '" . $_POST['editProduct'] . "' WHERE `groceries`.`id` =" . $_POST['modify'];
        if ($myResult = mysqli_query($myConnection, $myRequest)) {
            $fatal = "Modified " . $_POST['editProduct'] . ".";
        } else {
            $fatal = "Modification fail.";
        }
    } else {
        $fatal = "Please submit a non-empty value.";
    }
}

$myRequest = "SELECT * FROM `groceries` WHERE `purchased` = 0";
if ($myResult = mysqli_query($myConnection, $myRequest)) {
    createTable($myResult);
} else {
    echo "DB request failed.<br>";
}

$myRequest = "SELECT * FROM `groceries` WHERE `purchased` = 1 ORDER BY `product`";
if ($myResult = mysqli_query($myConnection, $myRequest)) {
    createTable($myResult);
} else {
    echo "DB request failed.<br>";
}

function createTable($myResult)
{
    global $tableContent;
    while ($currentResult = mysqli_fetch_array($myResult)) {
        $checked = "";
        $color = false;
        $colorClass = "";
        if ($currentResult['purchased'] == true) {
            $checked = " checked ";
            $color = true;
        }
        if ($color) {
            $colorClass = "class='myBg-success'";
        }
        $hiddenBox = "<input hidden type='checkbox' name='purchased' value='" . $currentResult['id'] . "' checked>";
        $tableContent .= "<tr " . $colorClass . ">";
        $tableContent .= "<td class='myTd'>" . $currentResult['product'] . "</td>";
        $tableContent .= "<td><form method='post'>" . $hiddenBox . "<input type='checkbox' onchange='submit()'" . $checked . "></form></td>";
        $tableContent .= "<td class='d-flex justify-content-center'><button class='btn myBtn-danger mr-1 modify d-none d-md-block' name='modif'>Modify</button><form method='post'>
        <button type='submit' class='btn myBtn-danger' name='del' value='" . $currentResult['id'] . "'>DELETE</button>
    </form></td>";
        $tableContent .= "</tr>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Chilanka&family=Poppins:wght@300&family=Sansita+Swashed&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>Karot</title>
</head>

<body>

    <?php

    if ($loggedIn == true) {
        echo '<div class="container-md">
    <div class="tableSurround">
        <table class="myTable">
            <thead>
                <tr class="w-100">
                    <th>Product</th>
                    <th>Already purchased</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                ' . $tableContent . '
            </tbody>
        </table>
    </div>
    <div class="surround shadow">
        ' . $fatal . '
        <form method="post" class="form-inline justify-content-center py-4">
            <div class="form-group">
                <label for="addProduct" class="mx-2">Product</label>
                <input type="text" name="addProduct" id="addProduct" class="form-control" autofocus required>
            </div>
            <input type="submit" class="btn myBtn-primary ml-2" value="Add">
        </form>
        <form method="post" class="form-inline justify-content-center">
            <button type="submit" class="btn btn-warning mb-3" name="logOut">Sign Out</button>
        </form>
    </div>
</div>';
    } elseif ($signUp == true) {
        include('monSignUp.php');
    } else {
        include('monLogin.php');
    }

    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.13.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type='text/javascript'>
        $(".myTd").click(function() {
            var id = $(this).siblings().last().children().children().val();
            var product = $(this).html();
            $(this).replaceWith("<td><form method='post' id='" + id + "'></form><input type='hidden' form='" + id + "' name='modify' value='" + id + "'><input type='text' form='" + id + "' value='" + product + "' class='form-control editInput' name='editProduct'></td>");
            $(".editInput").focus();
            $(".editInput").focusout(function() {
                if ($(".editInput").val() != product) {
                    $("#" + id).submit();
                } else {
                    window.location = window.location.href;
                }
            });
        })

        $(".modify").click(function() {
            $(this).parent().siblings(".myTd").trigger("click");
        })
    </script>
</body>

</html>