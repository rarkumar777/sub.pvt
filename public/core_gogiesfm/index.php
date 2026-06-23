<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                               ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                    ///
 * @link		    www.cms.gogies.net                             ///
 * @Version         1.0                                                   ///
 * @Created by      Ahmad Helalat                                         ///
 */                                                                       ///
//--------From the end of this line you can edit what ever you want ------///
define('gogies', true);
include '../../config/config.php';
include $GOGIES['path'] . '/core/init.php';
if (!$GOGIES['is_admin'])
{
    echo 'YOU MUST LOGIN AS ADMIN TO ACCESS THIS PAGE';
    exit;
}
include $GOGIES['admin_path'] . '/lang/' . $GOGIES['lang'] . '/lang_data.php';
$ext_img = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff'); //Images
//$ext_file = array('doc', 'docx', 'pdf', 'xls', 'xlsx', 'txt', 'csv','html','psd','sql','log','fla','xml','ade','adp','ppt','pptx'); //Files
//$ext_video = array('mov', 'mpeg', 'mp4', 'avi', 'mpg','wma'); //Videos
//$ext_music = array('mp3', 'm4a', 'ac3', 'aiff', 'mid'); //Music
//$ext_misc = array('zip', 'rar','gzip'); //Archives

$main_dir = $GOGIES['path'] . '/uploads/filemanager/';
$dir = $main_dir;
if (isset($_GET['dir']) && is_dir($main_dir . $_GET['dir']))
{
    $dir = $main_dir . $_GET['dir'];
}
if (isset($_GET['deldir']))
{

    $G['common']->remove_dir($dir . '/' . $_GET['deldir'] . '/');
    echo '<div id="dir_removed" style="position:fixed; top:0; height:0px;  overflow:visible; display:block; width:100%; z-index:9999;" class="animated zoomIn"><div style=" margin:auto; width:250px; position:relative">' . $G['common']->show_success_box($lang['success'] . '<a href="#" onClick="$(\'#dir_removed\').remove(); return false;" title="Close" class="close">&times</a>') . '
</div></div>';
    exit;
}
///////////////////////////////
if (isset($_GET['delfile']))
{

    @unlink($dir . '/' . $_GET['delfile']);
    echo '<div id="dir_removed" style="position:fixed; top:0; height:0px;  overflow:visible; display:block; width:100%; z-index:9999;" class="animated zoomIn"><div style=" margin:auto; width:250px; position:relative">' . $G['common']->show_success_box($lang['success'] . '<a href="#" onClick="$(\'#dir_removed\').remove(); return false;" title="Close" class="close">&times</a>') . '
</div></div>';
    exit;
}
/////////////////////////////
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_name']))
{
    if ($G['validate']->a_z_0_9($_POST['new_name']) &&
            file_exists($_POST['renamepath'] . '/' . $_POST['old_name']))
    {
       rename($_POST['renamepath'] . '/' . $_POST['old_name'], $_POST['renamepath'] . '/' . $_POST['new_name']);
       echo '<div id="dir_removed" style="position:fixed; top:50px; height:0px;  overflow:visible; display:block; width:100%; z-index:9999;" class="animated zoomIn d-pad"><div style=" margin:auto; width:250px; position:relative">' . $G['common']->show_success_box('<span style="font-size:16px;">'.$lang['success'] . '</span><a href="#" onClick="$(\'#dir_removed\').remove(); return false;" title="Close" class="close">&times</a>') . '
</div></div>';
    }
    else 
    {
    	echo '<div id="dir_removed" style="position:fixed; top:50px; height:0px;  overflow:visible; display:block; width:100%; z-index:9999;" class="animated zoomIn"><div style=" margin:auto; width:250px; position:relative">' . $G['common']->show_error_box($lang['name'].'->'.$lang['may_contain_only_numbers_letters'] . '<a href="#" onClick="$(\'#dir_removed\').remove(); return false;" title="Close" class="close">&times</a>') . '
</div></div>';
    }
}

/////////////////////////////
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']))
{
    if ($G['validate']->is_image('file'))
    {
        $tempFile = $_FILES['file']['tmp_name'];



        $targetFile = $dir . $_FILES['file']['name'];
        if (file_exists($targetFile))
        {
            $targetFile = $dir . time() . $_FILES['file']['name'];
        }
        if ($main_dir != $dir)
        {
            $targetFile = $dir . '/' . $_FILES['file']['name'];
        }

        $move = move_uploaded_file($tempFile, $targetFile);
        if ($move)
        {
            exit;
        } else
        {
            header("HTTP/1.1 404 Not Found");
            echo $lang['system_error'];
            exit;
        }
    } else
    {
        header("HTTP/1.1 404 Not Found");
        echo $lang['image'] . ' > ' . $lang['invalid_format'];
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gogies File Manager</title>
        <link href="<?= $GOGIES['url'] ?>/gogies3d/css/gogies.css" rel="stylesheet" media="all">
        <link href="<?= $GOGIES['url'] ?>/core/gogiesfm/dropzone.css" rel="stylesheet">
        <style>
            .items {list-style:none;}
            .items li{display:block; width:160px; margin:3px; padding:15px;  text-align:center; float:left; height:70px;  overflow:hidden; position:relative}
            .items li:hover i {color:rgba(245,236,150,1) !important; }
            .items li:hover { border:1px solid #ddd; overflow:visible }
            .items li i {color: rgba(244,227,110,1.00) !important;}
            .items li a{color:rgba(134,125,139,1.00)}
            .items li.image{ height:140px !important; width:160px !important; padding:0; margin:20px; overflow:hidden !important}
            .items .control{ display:none; position:absolute; top:0; left:0; color:#000; }
            .items .control:hover i{color:#000000 !important;}
            .items li:hover .control{display:block; z-index:99; }
            .breadcrumb a {
                line-height: 23px;
                padding: 0 8px 0 23px;}
            .breadcrumb i{ color: rgba(146,162,215,1.00) !important;}
            .breadcrumb a:after {
                content: '';
                position: absolute;
                top: 0;
                right: -10px;
                width:24px;
                height: 24px;
                -webkit-transform: scale(0.707) rotate(45deg);
                transform: scale(0.707) rotate(45deg);

                z-index: 1;
                background: #fff;
                -webkit-box-shadow:2px -2px 0 1px rgba(160, 160, 160, 0.3);
                box-shadow:2px -2px 0 1px rgba(160, 160, 160, 0.3);
                -webkit-border-radius: 0 5px 0 50px;border-radius: 0 5px 0 50px}
            .loader{ position:fixed; text-align:center; width:100%; height:100%; top:0; left:0; background-color:rgba(0,0,0,0.70); z-index:9999; vertical-align:middle; display: table;}
            .loader span{display:table-cell; vertical-align:middle;}
            .loader i{ color:#fff;}
        </style>
        <script src="<?= $GOGIES['url'] ?>/core/gogiesfm/dropzone.min.js"></script>

        <script>

            Dropzone.options.myAwesomeDropzone = {
                init: function () {
                    this.on("complete", function (file) {
                        $('#upload-close').attr('onClick', "window.location='#close'; window.location.reload();");
                    });
                }
            };

            function delete_file(file, id) {
                if (confirm('<?= $lang['sure_delete_item'] ?> ' + file) == true) {
                    do_ajax('#ajax', 'index.php?dir=<?= @$_GET['dir'] ?>&delfile=' + file, '');
                    $(id).remove();
                }
            }
            function delete_folder(file, id) {
                if (confirm('<?= $lang['sure_delete_item'] ?> ' + file) == true) {
                    do_ajax('#ajax', 'index.php?dir=<?= @$_GET['dir'] ?>&deldir=' + file, '');
                    $(id).remove();
                }
            }
        </script>

    </head>
    <body>

        <div class=" table full-width">
            <div class="fixed full-width" style="z-index:9999;">
                <div class="row grey borderd-b">
                    <div class="sd-5 pad"><h3 class="nopad"><i class="fa-folder-open"></i> <?= $lang['file_manager'] ?></h3></div>
                    <div class="sd-7">
                        <div class="row">
                            <div class="sd-12 align-right h-pad">
                                <a href="#new_folder" class="btn green round-corners small"><i class="fa-plus"></i> <?= $lang['new_folder'] ?></a>
                                <a href="#upload" class="btn orange round-corners small"><i class="fa-upload"></i> <?= $lang['upload'] ?></a>
                                <a href="#about" class="btn grey round-corners small circle h-pad"><i class="fa-info-circle"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="breadcrumb full-width">
                    <a href="?dir=" ><i class="fa-home"></i></a>
                    <?php
                    $bc = str_replace($main_dir, '', $dir);
                    $bc = explode('/', $bc);
                    if (is_array($bc) && count($bc) > 0)
                    {
                        $dir_link = NULL;
                        $dir_sep = NULL;
                        foreach ($bc as $sub_dir)
                        {

                            $dir_link .= $dir_sep . $sub_dir;
                            $dir_sep = '/';
                            echo '<a href="?dir=' . $dir_link . '" ><i class="fa-folder-open"></i> ' . $sub_dir . '</a>';
                        }
                    }
                    ?>
                </div></div>
            <div class="full-width" style="height:50px;"></div>
            <div id="ajax"></div>
            <?php
            $new_folder_msg = NULL;
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_folder']))
            {
                $_POST['new_folder'] = trim($_POST['new_folder']);
                $action = @mkdir($dir . '/' . $_POST['folder_name']);
                if ($action)
                {
                    $new_folder_msg = $G['common']->show_success_box($lang['success']);
                } else
                {
                    $new_folder_msg = $G['common']->show_error_box($lang['create_folder_error']);
                }
            }

            $folders = NULL;
            $files = NULL;
            $c = 1;
            foreach (new DirectoryIterator($dir) as $fileInfo)
            {
                $c = $c + 1;
                if ($fileInfo->isDot())
                    continue;
                $name = $fileInfo->getFilename();
                $link = $fileInfo->getPath();

                $link = str_replace($main_dir, '', $link);


                if ($dir == $main_dir)
                {
                    $link = $name;
                } else
                {
                    $link = $link . '/' . $name;
                }

                if ($fileInfo->isDir())
                {
                    $folders .= '<li id="' . $c . '"  title="' . $name . '"><div class="control"><a  href="#" onclick="delete_folder(\'' . $name . '\',\'#' . $c . '\'); return false;" class="btn red h-pad pad-l pad-r"><i class="fa-trash-o"></i></a></div>
		<a href="?dir=' . $link . '"><i class="fa-folder-open fa-2x"></i><br>
' . $name . '</a></li>';
                } else
                {

                    $ext = $fileInfo->getExtension();

                    if (in_array(strtolower($ext), $ext_img))
                    {
                        $files .= '<li id="' . $c . '" class="image" title="' . $name . '"><div class="control"><a  href="#" onclick="delete_file(\'' . $name . '\',\'#' . $c . '\'); return false;" class="btn red h-pad pad-l pad-r"><i class="fa-trash-o"></i></a>
<a href="#" class="btn green  h-pad pad-l pad-r" onClick="apply_img(\'' . $GOGIES['url'] . '/uploads/filemanager/' . $link . '\');">' . $lang['select'] . '</a>
    <a href="javascript:void"  class="btn orange h-pad rename_file" data-name="' . $name . '" data-path="' . $fileInfo->getPath() . '">' . $lang['rename'] . '</a>
		 </div><a href="#"
		 onclick=" view_image(\'' . $GOGIES['url'] . '/uploads/filemanager/' . $link . '\'); return false;"><img style="max-width:100%;" src="' . $GOGIES['url'] . '/uploads/filemanager/' . $link . '"><br>
' . $name . '</a></li>';
                    } else
                    {
                        $files .= '<li id="' . $c . '" title="' . $name . '"><div class="control"><a  href="#" onclick="delete_file(\'' . $name . '\',\'#' . $c . '\'); return false;" class="btn red h-pad pad-l pad-r"><i class="fa-trash-o"></i></a>
	 </div><a href="' . $GOGIES['url'] . '/uploads/filemanager/' . $link . '"><i class="fa-file-text-o fa-2x"></i><br>' . $name . '</a></li>';
                    }
                }
            }
            echo '<ul class="items">' . $folders . '</ul>';

            echo '<div class="row"></div><ul class="items">' . $files . '</ul>';
            ?>

            <div id="upload" class="modal">
                <div>
                    <a href="#close" onClick="" id="upload-close" title="Close" class="close">&times</a>
                    <h3><i class="fa-upload"></i> <?= $lang['upload'] ?></h3>

                    <form  action="index.php?dir=<?= @$_GET['dir'] ?>" method="post" enctype="multipart/form-data" id="myAwesomeDropzone" class="dropzone">
                        <div class="fallback">
                            <input name="file" type="file" />
                            <input type="submit" name="upload" value="OK" />
                        </div>
                    </form>



                </div>
            </div>
            <div id="new_folder" class="modal">
                <div>
                    <a href="#close" title="Close" class="close">&times</a>
                    <h3><i class="fa-plus"></i> <?= $lang['new_folder'] ?></h3>
                    <?= $new_folder_msg ?>
                    <form  method="post" >
                        <div class="row">
                            <div class="sd-6 pad d-pad-t"><strong><?= $lang['name'] ?></strong></div>
                            <div class="sd-6 pad"><input  type="text" name="folder_name" required></div>
                        </div>
                        <div class="d-pad align-center"><button class="btn blue" name="new_folder"><i class="fa-check"></i> <?= $lang['save'] ?></button></div>
                    </form>
                </div>
            </div>
            <div id="about" class="modal">
                <div>
                    <a href="#close" title="Close" class="close">&times</a>
                    <div class="pad align-center">
                        <strong>Gogies File Manager &copy; </strong><a href="http://gogies.net/" target="_blank" ><strong>GOGIES</strong></a>
                    </div>
                </div>
            </div>
        </div>
        <div id="image-view" class="modal">
            <div>
                <a href="#close" title="Close" class="close">&times</a>
                <b><i class="fa-eye"></i> <?= $lang['view'] ?></b>
                <div class="align-center pad-t">
                    <img id="image-view-src" src="" style="max-width:100%;">
                </div></div>
        </div>
        <div class="modal" id="rename_file">
            <div>
                <h3><?= $lang['rename'] ?></h3>
                <a href="#close" title="Close" class="close">&times</a>
                <form method="post" action="index.php?dir=<?= @$_GET['dir'] ?>">
                    <input type="text" id="new_name"  name="new_name" required>
                    <input type="hidden" id="old_name" name="old_name">
                    <input type="hidden" id="renamepath" name="renamepath">
                    <div class="pad align-center">
                        <input type="submit" name="rename" class="btn blue" value="OK" />
                    </div>
                </form>
            </div>
        </div>
        <script src="<?= $GOGIES['url'] ?>/gogies3d/js/jquery-3.1.1.min.js"></script>
        <script src="<?= $GOGIES['url'] ?>/gogies3d/js/gogies.js"></script>
        <script>
            function view_image(src) {
                $('#image-view-src').attr('src', src);
                window.location = '#image-view';
            }
            function apply_img(file) {
                if ($('#popup').val() == 1) {
                    var window_parent = window.opener;
                } else {
                    var window_parent = window.parent;
                }

                var target = window_parent.document.getElementById('gogies_img');
                var closed = window_parent.document.getElementById('gogiesfm');

                $(target).val(file);

                $(closed).find('.mce-close').trigger('click');
                $(closed).find('#closefm')[0].click();




            }
            $('.rename_file').on('click', function () {
                var name = $(this).attr('data-name');
                var path = $(this).attr('data-path');
                $('#renamepath').val(path);
                $('#old_name,#new_name').val(name);
                window.location = '#rename_file';
            })
        </script>
        <?php $G['common']->get_debug(); ?>

    </body>
</html>