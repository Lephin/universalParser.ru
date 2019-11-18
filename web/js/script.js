var y = 0;


/**
 * 
 * @returns {undefined}
 * Событме кнопки "Добавить"
 */
function add() 
{
    
    var addColumns = document.getElementById('addColumns').value;
    var getColumns = document.getElementById('getColumns');
    var addDrop = document.getElementById('addDrop');
    var elem = addDrop.parentNode;
    y++;
    var htmlInner = '<li id="liColumns" style="list-style-type: none;"><div id="' + y + '" class="textColumns">' + addColumns + '</div><div class="deleteColumns" onClick="deleteColumns(this)">Удалить</div></li>';

    getColumns.insertAdjacentHTML('beforeEnd',htmlInner);
    addOption(elem,addColumns);
}

function addOption(elem,addColumns)
{
    for (var i = 0;i < elem.parentNode.childNodes.length;i++) {
        elem.parentNode.childNodes[i].firstChild.insertAdjacentHTML('beforeEnd','<option id="' + y + '">' + addColumns + '</option>');
    }
}

function delOption(delOpt,elem)
{    
   for (var i = 0;i < elem.parentNode.childNodes.length;i++) {
        elem.parentNode.childNodes[i].firstChild.removeChild(elem.parentNode.childNodes[i].firstChild.querySelector('[id="' + delOpt.id + '"]'));
    }
}
 /**
  * 
  * @param {type} name
  * @returns {undefined}
  * Событие кнопки "Удалить"
  */
function deleteColumns(name)
{
    var liColumns = name.parentNode;
    var addDrop = document.getElementById('addDrop');
    var elem = addDrop.parentNode;
    var getColumns = document.getElementById('getColumns');
    
    getColumns.removeChild(liColumns);
    delOption(liColumns.firstChild,elem); 
}