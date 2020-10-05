<?php
/*
 *	if 'file_structure' isn't supplied, show welcome page
 */

if (empty($_GET['file_structure'])) {
    echo '<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Zyro File Structure Fixer</title>
    <!-- Custom CSS -->
    <style>
        .section-padding {
            padding-top: 5rem;
            padding-bottom: 5rem;
        }
        
        .masthead {
            height: 100vh!important;
            min-height: 100vh!important;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        body { padding-top: 2.5rem!important; }
    </style>
</head>
<body>
    <header class="masthead">
        <div class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <a class="navbar-brand" onClick="document.body.scrollTop = 0; document.documentElement.scrollTop = 0;" style="cursor: pointer;">
            Zyro File Structure Fixer
        </a>
        </div>
        <section>
            <div class="jumbotron jumbotron-fluid">
                <div class="container">
                    <h1 class="display-4">Zyro File Structure Fixer</h1>
                    <p class="lead">As of October 1, 2020, the old Zyro Site Builder has been deprecated and users can no longer edit their websites using the drag-and-drop builder. As such, users will need to make changes directly to individual HTML files in order to edit their site\'s content.</p>
                    <p class="lead">However, many users of Zyro are new to HTML/CSS and don\'t know where to start. To help ease this transition for inexperienced users, some members of the <a href="https://000webhost.com/forum" target="_blank">000webhost Forum</a> have created a script which organizes existing Zyro files and allows users to edit their existing HTML without having to deal with Zyro\'s confusing file structure.</p>
                    <p class="lead">Use this script to organize your existing Zyro site files. Editable HTML files will be located under your site\'s root directory in the <code>/html</code> folder, renamed according to the title of each page for ease of editing.</p>
                    <hr class="my-4">
                    <p style="color:red;">For this script to work, it must be placed in your site\'s root directory (usually <code>public_html</code> or <code>htdocs</code>).</p>
                </div>
            </div>
        </section>
        <section class="section-padding">
            <div class="container">
                <h1>Fix your site:</h1>
                <div class="row">
                    <div class="col-lg-12">
                        <h4>Reorganize file structure:</h4>
                        <p>Use this option to organize your existing Zyro site files. It moves all Zyro files to your site\'s root directory and creates a <code>/html</code> folder, renaming files according to the title of each page for ease of editing.</p>
                        <p style="color:red;">For this script to work, it must be placed in your site\'s root directory (usually <code>public_html</code> or <code>htdocs</code>).</p>
                        <p style="color:red;">This option may overwrite important files in your site\'s root directory. Make sure you\'ve taken a backup.</p>
                        <button class="btn btn-info" onclick="window.location=\'?file_structure=1\'">Fix structure</button>
                        <br>
                    </div>
                </div>
            </div>
        </section>
        <footer class="py-4 bg-dark text-white-50" id="footer">
            <div class="container text-left">
                <small>Created by <a href="https://www.000webhost.com/forum/u/teodor/summary">Teodor Ionescu</a> and <a href="https://www.000webhost.com/forum/u/sulliops/summary">Owen Sullivan</a>.</small>
            </div>
        </footer>
    </header>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>';
} /*
 *	if 'file_structure' is supplied, extract the numbered php files from zyro folder and rename them via their title
 */ else {
    if (is_dir("zyro")) {
        # carrige return delimiter used for sanitization
        $rn_delimiter = '#\r\n#';
        $z_delimiter = "\n";
        # require zyro index (to import pages router)
        require_once 'zyro/index.php';
        ob_clean();
        # mkdir for html sources
        mkdir(__DIR__ . '/zyro/html');
        # replace pages router file names
        foreach ($pages as $k => $page) {
            # rename file
            if (rename(__DIR__ . '/zyro/' . $page['file'], empty($page['alias']) ? __DIR__ . '/zyro/html/Home.php' : __DIR__ . '/zyro/html/' . $page['alias'] . '.php')) {
                # rename file in router
                $pages[$k]['file'] = empty($page['alias']) ? 'Home.php' : $page['alias'] . '.php';
            }
        }
        # get zyro index content
        $zi_content = str_replace($z_delimiter, $rn_delimiter, file_get_contents('zyro/index.php'));
        # create serialized router file
        file_put_contents("zyro/router.txt", serialize($pages));
        # replace the routes
        $zi_content = preg_replace('@\$pages = array\(.*?\);@', '$pages = unserialize(file_get_contents(dirname(__FILE__)."/router.txt"));', $zi_content);
        $zi_content = preg_replace('@\$fl = dirname\(__FILE__\)\.\'/\'\.\$page\[\'file\'\];@', '$fl = dirname(__FILE__).\'/html/\'.$page[\'file\'];', $zi_content);
        # update zyro index router
        file_put_contents('zyro/index.php', str_replace($rn_delimiter, $z_delimiter, $zi_content));
        #

        // report errors
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // check for up-to-date PHP version
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            echo "Your PHP version is outdated for this website. Please update PHP version to 5.3.6 or higher.";
            exit();
        }

        // get current directory (make sure this file is in "public_html")
        $currentdirectory = getcwd();

        // copy files and folders recursively out of "zyro" and into "public_html"
        function custom_copy($src, $dst)
        {
            // open the source directory
            $dir = opendir($src);
            // Make the destination directory if not exist
            @mkdir($dst);
            // Loop through the files in source directory
            while ($file = readdir($dir)) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($src . '/' . $file)) {
                        // Recursively calling custom copy function
                        // for sub directory
                        custom_copy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }
        $src = "zyro";
        $dst = $currentdirectory;
        custom_copy($src, $dst);

        // there are remaining ".htaccess" files preventing deletion of "zyro" folder, fixing that
        unlink("zyro/.htaccess");
        unlink("zyro/phpmailer/.htaccess");

        // remove "zyro" directory and all files recursively
        function removeDirectory($path)
        {
            $files = glob($path . '/*');
            foreach ($files as $file) {
                is_dir($file) ? removeDirectory($file) : unlink($file);
            }
            rmdir($path);
            return;
        }
        removeDirectory('zyro/');

        // echo success page
        echo '<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Success! - Zyro File Structure Fixer</title>
    <!-- Custom CSS -->
    <style>
        .masthead {
            height: 100vh!important;
            min-height: 100vh!important;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        body { padding-top: 2.5rem!important; }
    </style>
</head>
<body>
    <div class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <a class="navbar-brand" onClick="document.body.scrollTop = 0; document.documentElement.scrollTop = 0;" style="cursor: pointer;">
            Zyro File Structure Fixer
        </a>
    </div>
    <header class="masthead">
        <div class="container h-100">
            <div class="row h-100 my-0 align-items-center">
                <div class="col-12 text-center">
                    <h1 class="display-4">Success!</h1>
                    <p class="lead" id="lead-text">You\'ve successfully fixed your site\'s file structure, meaning you can now edit your HTML files from the <code>/html</code> folder.</p>
                </div>
            </div>
        </div>
    </header>
    <footer class="py-4 bg-dark text-white-50" id="footer">
        <div class="container text-left">
            <small>Created by <a href="https://www.000webhost.com/forum/u/teodor/summary">Teodor Ionescu</a> and <a href="https://www.000webhost.com/forum/u/sulliops/summary">Owen Sullivan</a>.</small>
        </div>
    </footer>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>';
    } else {
        die('<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Error - Zyro File Structure Fixer</title>
    <!-- Custom CSS -->
    <style>
        .masthead {
            height: 100vh!important;
            min-height: 100vh!important;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        body { padding-top: 2.5rem!important; }
    </style>
</head>
<body>
    <div class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <a class="navbar-brand" onClick="document.body.scrollTop = 0; document.documentElement.scrollTop = 0;" style="cursor: pointer;">
            Zyro File Structure Fixer
        </a>
    </div>
    <header class="masthead">
        <div class="container h-100">
            <div class="row h-100 my-0 align-items-center">
                <div class="col-12 text-center">
                    <h1 class="display-4">Error</h1>
                    <p class="lead" id="lead-text">An error has occured that prevented the script from fixing your site. Ensure this script is in your web root (<code>public_html</code> or <code>htdocs</code>) where your <code>zyro</code> folder is located, then try again.</p>
                    <p class="lead">If you\'ve already run the script before, running it again won\'t change anything (this could be the cause of the error).</p>
                </div>
            </div>
        </div>
    </header>
    <footer class="py-4 bg-dark text-white-50" id="footer">
        <div class="container text-left">
            <small>Created by <a href="https://www.000webhost.com/forum/u/teodor/summary">Teodor Ionescu</a> and <a href="https://www.000webhost.com/forum/u/sulliops/summary">Owen Sullivan</a>.</small>
        </div>
    </footer>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>');
    }
}
?>
