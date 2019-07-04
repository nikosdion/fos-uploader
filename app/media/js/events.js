/*
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

if (typeof akeeba === "undefined")
{
    var akeeba = {};
}

if (typeof akeeba.events === "undefined")
{
    akeeba.events = {};
    akeeba.events.modal = null;
}

akeeba.events.browse = function()
{
    akeeba.events.modal = akeeba.Modal.open({
        iframe: "index.php?view=media&task=browse",
        width:  "80%",
        height: "80%"
    });

    return false;
};

function useMediaFile(myFile)
{
    document.getElementById('image').value = myFile;
    akeeba.events.modal.close();
}