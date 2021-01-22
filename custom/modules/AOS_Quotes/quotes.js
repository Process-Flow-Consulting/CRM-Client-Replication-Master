/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/en/msa/master_subscription_agreement_11_April_2011.pdf
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
var count=0;
function openPopup(value,count){
    var popup_request_data={
        "call_back_function":"set_product_return",
        "form_name":"EditView",
        "field_to_name_array":{
            "id":"id",
            "name":"name",
            "cost_usdollar":"cost_usdollar",
            "list_usdollar":"list_usdollar",
            "discount_usdollar":"discount_usdollar",
            "mft_part_num":"mft_part_num",
            "pricing_factor":"pricing_factor",
            "type_id":"type_id",
            "tax_class":"tax_class",
            "tax_class_name":"tax_class_name",
            "description":"description"
        },
        "passthru_data":{
            "row_id":count
        }
    };
    
open_popup('AOS_ProductTemplates',600,400,'&tree=ProductsProd&name='+value,true,false,popup_request_data);
}
function toReadOnly(doc,count){
    if(doc.getElementById('product_template_id_'+count).value!='')

    {
        toggleTaxSelect(doc,count,true);
        setToReadOnly(doc.getElementById('cost_price_'+count));
        setToReadOnly(doc.getElementById('list_price_'+count));
        setToReadOnly(doc.getElementById('tax_class_name_'+count));
        setToReadOnly(doc.getElementById('mft_part_num_'+count));
    }else{
        toEdit(doc,count);
    }
}
function setToReadOnly(element){
    element.style.background='#dddddd';
    element.readOnly=true;
}
function toEdit(doc,count){
    toggleTaxSelect(doc,count,false);
    setToEdit(doc.getElementById('cost_price_'+count));
    setToEdit(doc.getElementById('list_price_'+count));
    setToEdit(doc.getElementById('discount_price_'+count));
    setToEdit(doc.getElementById('tax_class_name_'+count));
    setToEdit(doc.getElementById('mft_part_num_'+count));
}
function setToEdit(element){
    element.style.background='#ffffff';
    element.readOnly=false;
}
function taxSelectChanged(doc,count){
    doc.getElementById('tax_class_'+count).value=doc.getElementById('tax_class_select_name_'+count).options[doc.getElementById('tax_class_select_name_'+count).selectedIndex].value;
    calculate(doc);
}
function selectTax(doc,count){
    if(doc.getElementById('tax_class_name_'+count).value!=''){
        for(var i=0;i<doc.getElementById('tax_class_select_name_'+count).options.length;i++){
            if(doc.getElementById('tax_class_select_name_'+count).options[i].value==doc.getElementById('tax_class_name_'+count).value){
                doc.getElementById('tax_class_select_name_'+count).selectedIndex=i;
                return;
            }
        }
        }
}
function discount_calculated(doc,count){
    var discountAmount;
    if(doc.getElementById('checkbox_select_'+count).checked==true){
        doc.getElementById('discount_select_'+count).value=true;
        discountAmount=unformatNumber(doc.getElementById('discount_amount_'+count).value,num_grp_sep,dec_sep)/100*unformatNumber(doc.getElementById('discount_price_'+count).value,num_grp_sep,dec_sep);
    }
    else{
        doc.getElementById('discount_select_'+count).value=false;
        discountAmount=unformatNumber(doc.getElementById('discount_amount_'+count).value,num_grp_sep,dec_sep);
    }
    doc.getElementById('deal_calc_'+count).value=discountAmount;
    calculate(doc);
}
var holding_row_id='';
function copy_cell_children(from_cell,to_cell){}
function ungrab_table_row(){
    if(holding_row_id!=''){
        var from_row=document.getElementById(holding_row_id);
        if(typeof(from_row)=='undefined'||!from_row){
            holding_row_id='';
            return;
        }
        holding_row_id='';
        from_row.style.background='';
    }
}
function grab_table_row(row_id){
    if(holding_row_id==''){
        holding_row_id=row_id;
        var from_row=document.getElementById(holding_row_id);
        from_row.style.background='#666666';
    }else{
        var from_row=document.getElementById(holding_row_id);
        if(typeof(from_row)=='undefined'||!from_row){
            holding_row_id='';
            return;
        }
        var from_table=document.getElementById(from_row.tableId);
        if(typeof(from_table)=='undefined'||!from_table){
            holding_row_id='';
            return;
        }
        var from_body=from_table.tBodies[0];
        var from_index=from_row.rowIndex;
        var from_count=from_row.count;
        from_row.style.background='';
        var to_row=document.getElementById(row_id);
        to_row.style.background='';
        var to_table=document.getElementById(to_row.tableId);
        var to_body=to_table.tBodies[0];
        var to_index=to_row.rowIndex;
        var to_count=to_row.count;
        if(to_index<from_index){
            temp_id=holding_row_id;
            holding_row_id=row_id;
            return grab_table_row(temp_id)
            }
        var to_offset=0;
        if(to_table!=from_table){
            to_offset++;
        }
        var tempId=from_row.tableId;
        from_row.tableId=to_row.tableId;
        to_row.tableId=tempId;
        lookup_item('parent_group_'+to_count,document).value=to_row.tableId;
        lookup_item('parent_group_'+from_count,document).value=from_row.tableId;
        lookup_item('parent_group_index_'+from_count,document).value=to_index;
        lookup_item('parent_group_index_'+to_count,document).value=from_index;
        var from_position=lookup_item('parent_group_position_'+from_count,document).value;
        var to_position=lookup_item('parent_group_position_'+to_count,document).value;
        lookup_item('parent_group_position_'+from_count,document).value=to_position;
        lookup_item('parent_group_position_'+to_count,document).value=from_position;
        to_body.insertBefore(from_row,to_row);
        var insertTo=to_index+to_offset;
        var insertFrom=from_index;
        if(insertTo>=to_body.rows.length)
            insertTo=to_body.rows.length-1;
        if(insertFrom>=from_body.rows.length){
            insertFrom=from_body.rows.length-1;
        }
        if(insertFrom==0){
            from_body.appendChild(to_body.rows[insertTo]);
        }else{
            from_body.insertBefore(to_body.rows[insertTo],from_body.rows[insertFrom]);
        }
        holding_row_id='';
        calculate(document);
    }
}
function toggleTaxSelect(doc,count,hideselect){
    if(hideselect){
        doc.getElementById('taxselect'+count).style.display='none';
        doc.getElementById('taxinput'+count).style.display='inline';
    }else{
        doc.getElementById('taxselect'+count).style.display='inline';
        selectTax(doc,count);
        doc.getElementById('taxinput'+count).style.display='none';
        doc.getElementById('tax_class_'+count).value=doc.getElementById('tax_class_select_name_'+count).options[doc.getElementById('tax_class_select_name_'+count).selectedIndex].value;
    }
}
var tax_rate_keys=new Array();
var tax_rates=new Array();
function add_tax_class(name,value){
    tax_rate_keys.push(name);
    tax_rates[name]=value;
}
var item_list_MSI=new Array();
function lookup_item(id,doc){
    if(typeof(item_list_MSI[id])!='undefined'){
        return item_list_MSI[id];
    }
    return doc.getElementById(id);
}
var default_product_status='UNSET';
var invalidAmount='UNSET';
var selectButtonTitle='UNSET';
var selectButtonKey='UNSET';
var selectButtonValue='UNSET';
var deleteButtonName='UNSET';
var deleteButtonConfirm='UNSET';
var deleteGroupConfirm='UNSET';
var deleteButtonValue='UNSET';
var addRowName='UNSET';
var addRowValue='UNSET';
var deleteTableName='UNSET';
var deleteTableValue='UNSET';
var subtotal_string='UNSET';
var shipping_string='UNSET';
var deal_tot_string='UNSET';
var new_sub_string='UNSET';
var total_string='UNSET';
var tax_string='UNSET';
var addGroupName='UNSET';
var addGroupValue='UNSET';
var addCommentName='UNSET';
var addCommentValue='UNSET';
var deleteCommentName='UNSET';
var deleteCommentValue='UNSET';
var deleteCommentConfirm='UNSET';
var list_quantity_string='UNSET';
var list_product_name_string='UNSET';
var list_mf_part_num_string='UNSET';
var list_taxclass_string='UNSET';
var list_cost_string='UNSET';
var list_list_string='UNSET';
var list_discount_string='UNSET';
var list_deal_tot='UNSET';
var check_data='UNSET';
var table_list=new Array();
var blankDataLabel=document.createElement('td');
blankDataLabel.className='dataLabel';
blankDataLabel.width=90;
blankDataLabel.nowrap=true;
function deleteTable(id){
    table_array[id].splice(id,1);
    ungrab_table_row()
    lookup_item('delete_table_'+id,document).value=id;
    var table=lookup_item(id,document);
    var table_tally=lookup_item(id+'_tally',document);
    var table_header=lookup_item(id+'_header',document);
    var tables=document.getElementById('add_tables');
    tables.removeChild(table);
    tables.removeChild(table_tally);
    calculate(document);
    tables.removeChild(table_header);
    tables.removeChild(lookup_item(id+'_hr1',document));
    tables.removeChild(lookup_item(id+'_hr2',document));
}
table_array=new Array();
rows_nb_per_group=new Array();
function addTable(id,bundle_stage,bundle_name,bundle_shipping){
    if(id==''){
        id='group_'+count;
    }
    table_array[id]=new Array();
    rows_nb_per_group[id]=1;
    var form=document.getElementById('EditView');
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"delete_table["+id+"]");
    textEl.setAttribute('id',"delete_table_"+id);
    textEl.value=1;
    item_list_MSI["delete_table["+id+"]"]=textEl;
    form.appendChild(textEl);
    var tables=document.getElementById('add_tables');
    var tableEl=document.createElement('table');
    tableEl.setAttribute('name',id);
    tableEl.setAttribute('id',id);
    tableEl.border=0;
    tableEl.cellspacing=1;
    tableEl.cellpadding=0;
    item_list_MSI[id]=tableEl;
    var newHR=document.createElement('hr');
    newHR.id=id+'_hr1';
    var newDIV=document.createElement('div');
    newDIV.id=id+'_header';
    newDIV.innerHTML=getTableSettings(id);
    tables.appendChild(newHR);
    tables.appendChild(newDIV);
    tables.appendChild(tableEl);
    table_list.push(id);
    var newHR=document.createElement('hr');
    newHR.id=id+'_hr2';
    tables.appendChild(newHR);
    var tableEl=document.createElement('table');
    tableEl.setAttribute('name',id+'_tally');
    tableEl.setAttribute('id',id+'_tally');
    tableEl.border=0;
    tableEl.cellspacing=1;
    tableEl.cellpadding=0;
    item_list_MSI[id+'_tally']=tableEl;
    tables.appendChild(tableEl);
    addTableTally(id);
    addTableHeader(id);
    setTableSettingsValue(id,bundle_stage,bundle_name,bundle_shipping);
    count++;
    return id;
}
function table_exists(id){
    for(i=0;i<table_list.length;i++){
        if(table_list[i]==id){
            return true;
        }
    }
return false;
}
function setTableSettingsValue(id,stage_val,name_val,shipping_val){
    var select=document.getElementById('bundle_stage_'+id);
    for(var m=0;m<select.options.length;m++){
        if(select.options[m].value==stage_val){
            select.options[m].selected=true;
        }
    }
var name=document.getElementById('bundle_name_'+id);
name.value=name_val;
var shipping=document.getElementById('shipping_'+id);
shipping.value=formatNumber(shipping_val,num_grp_sep,dec_sep,precision,precision);
}
function getTableSettings(id){
    var temp_html=document.getElementById('ie_hack_stage').innerHTML;
    temp_html=temp_html.replace('select_id','bundle_stage_'+id);
    temp_html=temp_html.replace('select_name','bundle_stage['+id+']');
    temp_html=temp_html.replace('name_id','bundle_name_'+id);
    temp_html=temp_html.replace('name_name','bundle_name['+id+']');
    temp_html=temp_html.replace('table_id','bundle_header_'+id);
    temp_html=temp_html.replace('table_name','bundle_header['+id+']');
    return temp_html;
}
function addTableTally(id){
    var tableEl=document.getElementById(id+'_tally');
    var rowEl=tableEl.insertRow(tableEl.rows.length);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width='550';
    rowEl.appendChild(tdEl);
    var inputEl=document.createElement('input');
    inputEl.className='button';
    inputEl.type='button';
    inputEl.tableId=id;
    inputEl.onclick=function(){
        addRow("","","","",0,0,"","","","","","","",this.tableId,'','','','','','0','','','');
    }
    inputEl.name=addRowName;
    inputEl.value=addRowValue;
    tdEl.appendChild(inputEl);
    var inputEl=document.createElement('input');
    inputEl.className='button';
    inputEl.type='button';
    inputEl.tableId=id;
    inputEl.onclick=function(){
        addCommentRow("",this.tableId,"");
    }
    inputEl.name=addCommentName;
    inputEl.value=addCommentValue;
    tdEl.appendChild(inputEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.appendChild(document.createTextNode(subtotal_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=70;
    rowEl.appendChild(tdEl);
    var inputEl=document.createElement('input');
    inputEl.type='text';
    inputEl.size=15;
    inputEl.tabIndex=6;
    inputEl.name='subtotal['+id+']';
    inputEl.id='subtotal_'+id;
    inputEl.readOnly=true;
    inputEl.style.textAlign='right';
    setToReadOnly(inputEl);
    tdEl.appendChild(inputEl);
    item_list_MSI['subtotal_'+id]=inputEl;
    rowEl.appendChild(tdEl);
    var inputEl=document.createElement('input');
    inputEl.type='hidden';
    inputEl.size=15;
    inputEl.name='subtotal_usdollar['+id+']';
    inputEl.id='subtotal_usdollar'+id;
    inputEl.readOnly=true;
    tdEl.appendChild(inputEl);
    inputEl.style.textAlign='right';
    item_list_MSI['subtotal_usdollar'+id]=inputEl;
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    rowEl.appendChild(tdEl);
    var rowEl=tableEl.insertRow(tableEl.rows.length);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=550;
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.appendChild(document.createTextNode(deal_tot_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=70;
    rowEl.appendChild(tdEl);
    var inputEl=document.createElement('input');
    inputEl.type='text';
    inputEl.size=15;
    inputEl.tabIndex=6;
    inputEl.name='deal_tot['+id+']';
    inputEl.id='deal_tot_'+id;
    inputEl.onchange=function(){
        if(isAmount(toDecimal(unformatNumber(this.value,num_grp_sep,dec_sep),precision))){
            calculate(document);
        }else{
            alert(invalidAmount);
            this.select()
            };
        
}
inputEl.readOnly=true;
inputEl.style.textAlign='right';
setToReadOnly(inputEl);
item_list_MSI['deal_tot_'+id]=inputEl;
tdEl.appendChild(inputEl);
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
rowEl.appendChild(tdEl);
var rowEl=tableEl.insertRow(tableEl.rows.length);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=550;
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.appendChild(document.createTextNode(new_sub_string));
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=70;
rowEl.appendChild(tdEl);
var inputEl=document.createElement('input');
inputEl.type='text';
inputEl.size=15;
inputEl.tabIndex=6;
inputEl.name='new_sub['+id+']';
inputEl.id='new_sub_'+id;
inputEl.onchange=function(){
    if(isAmount(toDecimal(unformatNumber(this.value,num_grp_sep,dec_sep),precision))){
        calculate(document);
    }else{
        alert(invalidAmount);
        this.select()
        };
    
}
inputEl.readOnly=true;
inputEl.style.textAlign='right';
setToReadOnly(inputEl);
item_list_MSI['new_sub_'+id]=inputEl;
tdEl.appendChild(inputEl);
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
rowEl.appendChild(tdEl);
var rowEl=tableEl.insertRow(tableEl.rows.length);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=550;
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.appendChild(document.createTextNode(tax_string));
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=70;
rowEl.appendChild(tdEl);
var inputEl=document.createElement('input');
inputEl.type='text';
inputEl.size=15;
inputEl.tabIndex=6;
inputEl.name='tax['+id+']';
inputEl.id='tax_'+id;
inputEl.readOnly=true;
inputEl.style.textAlign='right';
setToReadOnly(inputEl);
tdEl.appendChild(inputEl);
item_list_MSI['tax_'+id]=inputEl;
rowEl.appendChild(tdEl);
var inputEl=document.createElement('input');
inputEl.type='hidden';
inputEl.size=15;
inputEl.name='tax_usdollar['+id+']';
inputEl.id='tax_usdollar_'+id;
inputEl.readOnly=true;
inputEl.style.textAlign='right';
tdEl.appendChild(inputEl);
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
rowEl.appendChild(tdEl);
var rowEl=tableEl.insertRow(tableEl.rows.length);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=550;
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.appendChild(document.createTextNode(shipping_string));
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=70;
rowEl.appendChild(tdEl);
var inputEl=document.createElement('input');
inputEl.type='text';
inputEl.size=15;
inputEl.tabIndex=6;
inputEl.name='shipping['+id+']';
inputEl.id='shipping_'+id;
inputEl.onchange=function(){
    if(isAmount(toDecimal(unformatNumber(this.value,num_grp_sep,dec_sep),precision))){
        calculate(document);
    }else{
        alert(invalidAmount);
        this.select()
        };
    
}
inputEl.readOnly=false;
inputEl.style.textAlign='right';
item_list_MSI['shipping_'+id]=inputEl;
tdEl.appendChild(inputEl);
rowEl.appendChild(tdEl);
var inputEl=document.createElement('input');
inputEl.type='hidden';
inputEl.size=15;
inputEl.name='shipping_usdollar['+id+']';
inputEl.id='shipping_usdollar_'+id;
inputEl.readOnly=true;
inputEl.style.textAlign='right';
tdEl.appendChild(inputEl);
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
rowEl.appendChild(tdEl);
var rowEl=tableEl.insertRow(tableEl.rows.length);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=550;
rowEl.appendChild(tdEl);
var inputEl=document.createElement('input');
inputEl.className='button';
inputEl.type='button';
inputEl.tableId=id;
inputEl.onclick=function(){
    if(confirm(deleteGroupConfirm)){
        deleteTable(this.tableId);
    }
}
inputEl.name=deleteTableName;
inputEl.value=deleteTableValue;
tdEl.appendChild(inputEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.appendChild(document.createTextNode(total_string));
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=70;
var inputEl=document.createElement('input');
inputEl.type='text';
inputEl.size=15;
inputEl.tabIndex=6;
inputEl.name='total['+id+']';
inputEl.id='total_'+id;
inputEl.readOnly=true;
inputEl.style.textAlign='right';
setToReadOnly(inputEl);
tdEl.appendChild(inputEl);
item_list_MSI['total_'+id]=inputEl;
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
tdEl.width=70;
var inputEl=document.createElement('input');
inputEl.type='hidden';
inputEl.size=15;
inputEl.name='total_usdollar['+id+']';
inputEl.id='total_usdollar_'+id;
inputEl.style.textAlign='right';
inputEl.readOnly=true;
tdEl.appendChild(inputEl);
rowEl.appendChild(tdEl);
var tdEl=blankDataLabel.cloneNode(false);
rowEl.appendChild(tdEl);
}
function addTableHeader(id){
    var tableEl=document.getElementById(id);
    var rowEl=tableEl.insertRow(tableEl.rows.length);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=1;
    tdEl.appendChild(document.createTextNode(''));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=55;
    tdEl.appendChild(document.createTextNode(list_quantity_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=200;
    tdEl.appendChild(document.createTextNode(list_product_name_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=55;
    tdEl.appendChild(document.createTextNode(''));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=77;
    tdEl.appendChild(document.createTextNode(list_mf_part_num_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=100;
    tdEl.appendChild(document.createTextNode(list_taxclass_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=75;
    tdEl.appendChild(document.createTextNode(list_cost_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=75;
    tdEl.appendChild(document.createTextNode(list_list_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.appendChild(document.createTextNode(list_discount_string));
    rowEl.appendChild(tdEl);
    var tdEl=blankDataLabel.cloneNode(false);
    tdEl.width=75;
    tdEl.appendChild(document.createTextNode(list_deal_tot));
    rowEl.appendChild(tdEl);
}
function addCommentRow(id,table_id,comment_description){
    var form=document.getElementById('EditView');
    var table=document.getElementById(table_id);
    var row=table.insertRow(table.rows.length);
    var rowName='item_row_'+count;
    row.setAttribute('valign','top');
    row.id=rowName;
    row.tableId=table.id;
    row.count=count;
    var cell=row.insertCell(row.cells.length);
    cell.nowrap='nowrap';
    var buttonEl=document.createElement('input');
    buttonEl.setAttribute('type','button');
    buttonEl.count=count;
    buttonEl.onclick=function(){
        grab_table_row('item_row_'+this.count);
    }
    buttonEl.setAttribute('name','||');
    buttonEl.setAttribute('value','||');
    buttonEl.className='button';
    cell.appendChild(buttonEl);
    var cell=row.insertCell(row.cells.length);
    cell.colSpan="8";
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"comment_id["+count+"]");
    textEl.setAttribute('id',"comment_id_"+count);
    textEl.value=id;
    form.appendChild(textEl);
    var textEl=document.createElement('textarea');
    textEl.setAttribute('rows',3);
    textEl.setAttribute('cols',120);
    textEl.count=count;
    comment_description=comment_description.replace(/&#039;/g,'\'');
    textEl.value=comment_description.replace(/<br>/g,'\n');
    textEl.setAttribute('name',"comment_description["+count+"]");
    cell.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"comment_index["+count+"]");
    textEl.setAttribute('id',"comment_index"+count);
    textEl.setAttribute('value',count);
    cell.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"comment_delete["+count+"]");
    textEl.setAttribute('id',"comment_delete_"+count);
    textEl.value=1;
    form.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"parent_group["+count+"]");
    textEl.setAttribute('id',"parent_group_"+count);
    textEl.setAttribute('value',table_id);
    form.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"parent_group_index["+count+"]");
    textEl.setAttribute('id',"parent_group_index"+count);
    textEl.setAttribute('value',row.rowIndex);
    item_list_MSI["parent_group_index_"+count]=textEl;
    cell.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden');
    textEl.setAttribute('name',"parent_group_position["+count+"]");
    textEl.setAttribute('id',"parent_group_position"+count);
    textEl.setAttribute('value',count);
    item_list_MSI["parent_group_position_"+count]=textEl;
    cell.appendChild(textEl);
    var cell=row.insertCell(row.cells.length);
    var buttonEl=document.createElement('input');
    buttonEl.setAttribute('type','button');
    buttonEl.setAttribute('id','delete_comment'+count);
    buttonEl.tableId=table_id;
    buttonEl.count=count;
    buttonEl.onclick=function(){
        if(confirm(deleteCommentConfirm)){
            deleteCommentRow(row.count,row.tableId);
        }
    }
buttonEl.setAttribute('name',deleteCommentName);
buttonEl.setAttribute('value',deleteCommentValue);
buttonEl.className='button';
cell.appendChild(buttonEl);
rows_nb_per_group[table_id]=rows_nb_per_group[table_id]+1;
count++;
document.getElementById('product_count').value=count;
}
function addRow(id,quantity,product_template_id,product_name,cost_price,list_price,discount_price,pricing_formula,pricing_formula_name,pricing_factor,tax_class,tax_class_name,mft_part_num,table_id,bundle_stage,bundle_name,bundle_shipping,product_description,type_id,discount_amount,discount_select,deal_calc,product_status)
{ 
    if(!table_exists(table_id)){
        table_id=addTable(table_id,bundle_stage,bundle_name,bundle_shipping);
    }
    the_quantity=(quantity=='')?1:quantity;
    var unit_price;
    if(discount_price=='')unit_price='0'+dec_sep+'00';else unit_price=discount_price;
    if(discount_select=='')discount_select=false;
    if(deal_calc=='')deal_calc=0;
    if(product_status=='')product_status=default_product_status;
    var form=document.getElementById('EditView')
    var table=document.getElementById(table_id);
    var row=table.insertRow(table.rows.length);
    var rowName='item_row_'+count;
    table_array[table_id][rows_nb_per_group[table_id]]=parseFloat(count);
    rows_nb_per_group[table_id]=rows_nb_per_group[table_id]+1;
    var sqs_id=form.id+'_product_name['+count+']';
    sqs_objects[sqs_id]={
        "id":sqs_id,
        "form":form.id,
        "method":"query",
        "modules":["AOS_ProductTemplates"],
        "group":"or",
        "field_list":["name","id","type_id","mft_part_num","cost_price","list_price","discount_price","tax_class","pricing_factor","description","cost_usdollar","list_usdollar","discount_usdollar","tax_class_name","unit_measure"],
        "populate_list":["name_"+count,"product_template_id_"+count],
        "conditions":[{
            "name":"name",
            "op":"like_custom",
            "end":"%",
            "value":""
        }],
        "order":"name",
        "limit":"30",
        "no_match_text":sqs_no_match_text,
        "post_onblur_function":"set_after_sqs"
    };
    
    row.setAttribute('valign','top');
    row.id=rowName;
    row.tableId=table.id;
    row.count=count;
    var cell=row.insertCell(row.cells.length);
    cell.nowrap='nowrap';
    var buttonEl=document.createElement('input');
    buttonEl.setAttribute('type','button');
    buttonEl.count=count;
    buttonEl.onclick=function(){
        grab_table_row('item_row_'+this.count);
    }
    buttonEl.setAttribute('name','||');
    buttonEl.setAttribute('value','||');
    buttonEl.className='button';
    cell.appendChild(buttonEl);
    var cell=row.insertCell(row.cells.length);
    cell.nowrap='nowrap';
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"product_id["+count+"]");
    textEl.setAttribute('id',"product_id_"+count);
    textEl.value=id;
    item_list_MSI["product_id_["+count+"]"]=textEl;
    form.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"delete["+count+"]");
    textEl.setAttribute('id',"delete_"+count);
    textEl.value=1;
    item_list_MSI["delete["+count+"]"]=textEl;
    form.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"type_id["+count+"]");
    textEl.setAttribute('id',"type_id_"+count);
    textEl.value=type_id;
    item_list_MSI["type_id["+count+"]"]=textEl;
    form.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"product_template_id["+count+"]");
    textEl.setAttribute('id',"product_template_id_"+count);
    textEl.setAttribute('value',product_template_id);
    item_list_MSI["product_template_id["+count+"]"]=textEl;
    form.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"status["+count+"]");
    textEl.setAttribute('id',"status["+count+"]");
    textEl.setAttribute('value',product_status);
    item_list_MSI["status["+count+"]"]=textEl;
    form.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"tax_class["+count+"]");
    textEl.setAttribute('id',"tax_class_"+count);
    textEl.setAttribute('value',tax_class);
    item_list_MSI["tax_class["+count+"]"]=textEl;
    cell.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"parent_group["+count+"]");
    textEl.setAttribute('id',"parent_group_"+count);
    textEl.setAttribute('value',table_id);
    item_list_MSI["parent_group_"+count]=textEl;
    cell.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"parent_group_index["+count+"]");
    textEl.setAttribute('id',"parent_group_index"+count);
    textEl.setAttribute('value',row.rowIndex);
    item_list_MSI["parent_group_index_"+count]=textEl;
    cell.appendChild(textEl);
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden');
    textEl.setAttribute('name',"parent_group_position["+count+"]");
    textEl.setAttribute('id',"parent_group_position"+count);
    textEl.setAttribute('value',count);
    item_list_MSI["parent_group_position_"+count]=textEl;
    cell.appendChild(textEl);
    cell.width=55;
    var textEl=document.createElement('input');
    var quantName='quantity_'+count;
    textEl.setAttribute('type','text');
    textEl.size=4;
    textEl.tabIndex=6;
    textEl.setAttribute('name',"quantity["+count+"]");
    textEl.setAttribute('id',"quantity_"+count);
    textEl.setAttribute('value',the_quantity);
    item_list_MSI["quantity["+count+"]"]=textEl;
    textEl.onchange=function(){
        if(isInteger(lookup_item(quantName,document).value)){
            calculate(document);
        }else{
            alert(invalidAmount);
            alert(lookup_item(quantName,document).value);
            lookup_item(quantName,document).select();
        }
    }
cell.appendChild(textEl);
var cell1=row.insertCell(row.cells.length);
cell1.width=200;
cell1.noWrap=true;
var itemName='name_'+count;
var textEl=document.createElement('input');
textEl.setAttribute('type','text')
textEl.size=30;
textEl.tabIndex=6;
textEl.count=count;
textEl.value=product_name;
textEl.alt=function(){
    lookup_item(itemName,document);
}
textEl.className+="sqsEnabled sqsNoAutofill";
textEl.setAttribute('name',"product_name["+count+"]");
textEl.setAttribute('id',itemName);
textEl.onchange=function(){
    toEdit(document,this.count);
}
item_list_MSI[itemName]=textEl;
cell1.appendChild(textEl);
cell1.appendChild(document.createElement('div'));
var itemName='description_'+count;
var textEl=document.createElement('textarea');
textEl.setAttribute('rows',3);
textEl.setAttribute('cols',30);
textEl.count=count;
product_description=product_description.replace(/&#039;/g,'\'');
textEl.value=product_description.replace(/<br>/g,'\n');
textEl.alt=function(){
    lookup_item(itemName,document);
}
textEl.setAttribute('name',"product_description["+count+"]");
textEl.setAttribute('id',itemName);
item_list_MSI[itemName]=textEl;
cell1.appendChild(textEl);
var cellb=row.insertCell(row.cells.length);
cellb.width=55;
cellb.noWrap=true;
var spanEl=document.createElement('span');
spanEl.className='id-ff';
cellb.appendChild(spanEl);
var buttonEl=document.createElement('button');
var itemName='product_name_select_'+count;
buttonEl.setAttribute('type','button');
buttonEl.title=selectButtonTitle;
buttonEl.accessKey=selectButtonKey;
buttonEl.value=selectButtonValue;
buttonEl.innerHTML='<img src="index.php?entryPoint=getImage&imageName=id-ff-select.png&themeName='+SUGAR.themes.theme_name+'">';
buttonEl.textElement='name_'+count;
buttonEl.count=count;
buttonEl.tabIndex=6;
buttonEl.setAttribute('name',"btn_product_name["+count+"]");
buttonEl.setAttribute('id',itemName);
buttonEl.onclick=function(){
    openPopup(lookup_item(this.textElement,document).value,this.count);
}
buttonEl.className='button';
spanEl.appendChild(buttonEl);
var cell2=row.insertCell(row.cells.length);
cell2.width=75;
var textEl=document.createElement('input');
var itemName='mft_part_num_'+count;
textEl.setAttribute('type','text')
textEl.size=10;
textEl.setAttribute('name',"mft_part_num["+count+"]");
textEl.tabIndex=6;
textEl.setAttribute('id',itemName);
textEl.setAttribute('value',mft_part_num);
item_list_MSI[itemName]=textEl;
cell2.appendChild(textEl);
var textEl=document.createElement('input');
var itemName='pricing_factor_'+count;
textEl.setAttribute('type','hidden')
textEl.size=4;
textEl.setAttribute('name',"pricing_factor["+count+"]");
textEl.setAttribute('id',itemName);
textEl.setAttribute('value',pricing_factor);
item_list_MSI[itemName]=textEl;
cell2.appendChild(textEl);
var divselect=document.createElement('div');
divselect.setAttribute('id','taxselect'+count);
divselect.style.display='none';
item_list_MSI['taxselect'+count]=divselect;
var cell3=row.insertCell(row.cells.length);
cell3.width=100;
var selectEl=document.createElement('select');
selectEl.count=count;
selectEl.onchange=function(){
    taxSelectChanged(document,this.count);
}
var itemName='tax_class_select_name_'+count;
selectEl.setAttribute('name',"tax_class_select_name["+count+"]");
selectEl.setAttribute('id',itemName);
selectEl.tabIndex=6;
for(i=0;i<tax_rate_keys.length;i++){
    var optionEl=document.createElement('option');
    optionEl.text=tax_rate_keys[i];
    optionEl.value=tax_rates[optionEl.text];
    try{
        selectEl.add(optionEl,null);
    }catch(ex){
        selectEl.add(optionEl);
    }
}
divselect.appendChild(selectEl);
cell3.appendChild(divselect);
item_list_MSI[itemName]=selectEl;
var divnoselect=document.createElement('div');
divnoselect.setAttribute('id','taxinput'+count);
divnoselect.style.display='none';
item_list_MSI['taxinput'+count]=divselect;
var textEl=document.createElement('input');
var itemName='tax_class_name_'+count;
textEl.setAttribute('type','input');
textEl.size=8;
textEl.tabIndex=6;
textEl.setAttribute('name','tax_class_name['+count+']');
textEl.setAttribute('id',itemName);
textEl.setAttribute('value',tax_class_name);
item_list_MSI[itemName]=textEl;
divnoselect.appendChild(textEl);
cell3.appendChild(divnoselect);
var cell4=row.insertCell(row.cells.length);
cell4.width=75;
var textEl=document.createElement('input');
var itemName='cost_price_'+count;
textEl.setAttribute('type','text')
textEl.size=8;
textEl.style.textAlign='right';
textEl.tabIndex=6;
textEl.setAttribute('name',"cost_price["+count+"]");
textEl.setAttribute('id',itemName);
textEl.setAttribute('value',cost_price);
textEl.onchange=function(){
    if(!isAmount(toDecimal(unformatNumber(this.value,num_grp_sep,dec_sep),precision))){
        alert(invalidAmount);
        this.select();
    }
};

item_list_MSI[itemName]=textEl;
cell4.appendChild(textEl);
var cell5=row.insertCell(row.cells.length);
cell5.width=75;
var textEl=document.createElement('input');
var itemName='list_price_'+count;
textEl.setAttribute('type','text')
textEl.size=8;
textEl.style.textAlign='right';
textEl.tabIndex=6;
textEl.setAttribute('name',"list_price["+count+"]");
textEl.setAttribute('id',itemName);
textEl.setAttribute('value',list_price);
textEl.onchange=function(){
    if(!isAmount(toDecimal(unformatNumber(this.value,num_grp_sep,dec_sep),precision))){
        alert(invalidAmount);
        this.select();
    }
};

item_list_MSI[itemName]=textEl;
cell5.appendChild(textEl);
var cell6=row.insertCell(row.cells.length);
cell6.width=80;
var textEl=document.createElement('input');
var itemName='discount_price_'+count;
textEl.setAttribute('type','text')
textEl.size=8;
textEl.style.textAlign='right';
textEl.tabIndex=6;
textEl.setAttribute('name',"discount_price["+count+"]");
textEl.setAttribute('id',itemName);
textEl.setAttribute('value',unit_price);
textEl.onchange=function(){
    if(isAmount(toDecimal(unformatNumber(this.value,num_grp_sep,dec_sep),precision))){
        calculate(document);
    }else{
        alert(invalidAmount);
        this.select();
    }
};

item_list_MSI[itemName]=textEl;
cell6.appendChild(textEl);
var params=textEl.value;
var params1='"'+count+'", "'+id+'"';
var cell7=row.insertCell(row.cells.length);
cell7.width=60;
var divselect=document.createElement('div');
divselect.setAttribute('id','discount_amount_div'+count);
item_list_MSI['discount_amount_div'+count]=divselect;
cell7.appendChild(divselect);
var textEl=document.createElement('input');
var itemName='discount_amount_'+count;
textEl.setAttribute('type','text')
textEl.size=4;
textEl.style.textAlign='right';
textEl.tabIndex=6;
textEl.setAttribute('name',"discount_amount["+count+"]");
textEl.setAttribute('id',itemName);
textEl.setAttribute('value',discount_amount);
textEl.count=count;
textEl.onchange=function(){
    if(isAmount(toDecimal(unformatNumber(this.value,num_grp_sep,dec_sep),precision))){
        discount_calculated(document,this.count);
    }else{
        alert(invalidAmount);
        this.select();
    }
};

item_list_MSI[itemName]=textEl;
divselect.appendChild(textEl);
var params1='"'+count+'", "'+id+'"';
var cell8=row.insertCell(row.cells.length);
cell8.width=20;
var newtext=document.createTextNode("in\u00A0%");
cell8.appendChild(newtext);
var cell9=row.insertCell(row.cells.length);
cell9.width=50;
var ele2=document.createElement('td');
var textEl=document.createElement('input');
var itemName='checkbox_select_'+count;
textEl.setAttribute('name',"checkbox_select["+count+"]");
textEl.setAttribute('id',itemName);
textEl.setAttribute('type','checkbox');
textEl.setAttribute('class','checkbox');
textEl.setAttribute('value','1');
textEl.tabIndex=6;
textEl.count=count;
textEl.onclick=function(){
    discount_calculated(document,this.count);
}
cell9.appendChild(ele2);
ele2.appendChild(textEl);
if(discount_select==true){
    textEl.setAttribute('checked',true);
}
item_list_MSI[itemName]=textEl;
var divnoselect=document.createElement('div');
divnoselect.setAttribute('id','deal_calc'+count);
divnoselect.style.display='none';
item_list_MSI['deal_calc'+count]=divselect;
var inputselect=document.createElement('input');
inputselect.setAttribute('name',"discount_select["+count+"]");
var itemName='discount_select_'+count;
inputselect.setAttribute('id',itemName);
inputselect.setAttribute('value',discount_select);
divnoselect.appendChild(inputselect);
item_list_MSI[itemName]=inputselect;
var textEl=document.createElement('input');
var itemName='deal_calc_'+count;
textEl.setAttribute('type','input');
textEl.size=8;
textEl.tabIndex=2;
textEl.setAttribute('name','deal_calc['+count+']');
textEl.setAttribute('id',itemName);
textEl.setAttribute('value',deal_calc);
item_list_MSI[itemName]=textEl;
divnoselect.appendChild(textEl);
cell9.appendChild(divnoselect);
var cell10=row.insertCell(row.cells.length);
var buttonEl=document.createElement('input');
buttonEl.setAttribute('type','button');
buttonEl.setAttribute('id','delete_row'+count);
buttonEl.tableId=table_id;
buttonEl.tabIndex=6;
buttonEl.count=count;
buttonEl.onclick=function(){
    if(confirm(deleteButtonConfirm)){
        deleteRow(this.count,this.tableId);
        calculate(document);
    }
}
buttonEl.setAttribute('name',deleteButtonName);
buttonEl.setAttribute('value',deleteButtonValue);
buttonEl.className='button';
cell10.appendChild(buttonEl);
toEdit(document,this.count);
toReadOnly(document,count);
registerSingleSmartInputListener(document.getElementById('name_'+count));
count++;
document.getElementById('product_count').value=count;
calculate(document);
}

function hasAttribute(element,attr){
    if(element.hasAttribute)return element.hasAttribute(attr);
    return(typeof(element.getAttribute(attr))==typeof(''));
}
function deleteRow(id,table_id){    
	for(var i in table_array[table_id]){        
		if(table_array[table_id][i]==id){
            table_array[table_id].splice(i,1);
        }
    }
ungrab_table_row();
var table=document.getElementById(table_id);
var rows=table.rows;
var looking_for='delete_row'+id;
for(i=1;i<rows.length;i++){
    cells=rows[i].cells;
    for(var j=9;j<rows[i].cells.length;j++){
        cell=rows[i].cells[j];
        children=cell.childNodes;
        for(var k=0;k<children.length;k++){
            var child=children[k];
            if(child.nodeType==1&&hasAttribute(child,'id')){
                if(child.getAttribute('id')==looking_for){
                    table.deleteRow(i);
                    document.getElementById('delete_'+id).value=document.getElementById('product_id_'+id).value;
                    return;
                }
            }
        }
    }
}
}
function deleteCommentRow(id,table_id)
{
    ungrab_table_row();
    var table=document.getElementById(table_id);
    var rows=table.rows;
    var looking_for='item_row_'+id;
    for(var i=0;i<rows.length;i++){
        if(rows[i].id==looking_for){
            table.deleteRow(i);
            document.getElementById('comment_delete_'+id).value=document.getElementById('comment_id_'+id).value;
            return;
        }
    }
    }
function toggleDisplay(id){
    if(this.document.getElementById(id).style.display=='none'){
        this.document.getElementById(id).style.display='inline'
        if(this.document.getElementById(id+"link")!=undefined){
            this.document.getElementById(id+"link").style.display='none';
        }
    }else{
    this.document.getElementById(id).style.display='none'
    if(this.document.getElementById(id+"link")!=undefined){
        this.document.getElementById(id+"link").style.display='inline';
    }
}
}
function calculate(doc){
    var gt=Array();
    warned=false;
    gt['tax']=0;
    gt['subtotal']=0;
    gt['total']=0;
    gt['shipping']=0;
    gt['discount']=0;
    gt['new_sub']=0;
    for(var table_count=0;table_count<table_list.length;table_count++){
        cur_table_id=table_list[table_count];
        var table=doc.getElementById(cur_table_id);
        if(table!=null&&typeof(table)!='undefined'){
            var bundle_stage=doc.getElementById("bundle_stage_"+cur_table_id).value;
            var is_custom_group_stage=isCustomGroupStage(bundle_stage);
            if(!is_custom_group_stage){
                var retval=calculate_table(doc,cur_table_id);
                gt['tax']+=retval['tax'];
                gt['subtotal']+=retval['subtotal'];
                gt['discount']+=retval['discount'];
                gt['total']+=retval['total'];
                gt['new_sub']+=retval['new_sub'];
                if(retval['shipping']!='')gt['shipping']+=parseFloat(retval['shipping']);
            }
        }
    }
lookup_item('grand_total',document).innerHTML=formatNumber(toDecimal(gt['total'],precision),num_grp_sep,dec_sep,precision,precision);
lookup_item('grand_ship',document).innerHTML=formatNumber(toDecimal(gt['shipping'],precision),num_grp_sep,dec_sep,precision,precision);
lookup_item('grand_tax',document).innerHTML=formatNumber(toDecimal(gt['tax'],precision),num_grp_sep,dec_sep,precision,precision);
lookup_item('grand_new_sub',document).innerHTML=formatNumber(toDecimal(gt['new_sub'],precision),num_grp_sep,dec_sep,precision,precision);
lookup_item('grand_discount',document).innerHTML=formatNumber(toDecimal(gt['discount'],precision),num_grp_sep,dec_sep,precision,precision);
lookup_item('grand_sub',document).innerHTML=formatNumber(toDecimal(gt['subtotal'],precision),num_grp_sep,dec_sep,precision,precision);
}
function calculate_table(doc,table_id){
    var retval=Array();
    retval['subtotal']=calculate_subtotal(doc,table_id);
    retval['discount']=calculate_discount(doc,table_id);
    retval['new_sub']=calculate_new_sub(doc,table_id);
    retval['tax']=calculate_tax(doc,table_id);
    retval['total']=calculate_total(doc,table_id);
    retval['shipping']=unformatNumber(lookup_item('shipping_'+table_id).value,num_grp_sep,dec_sep);
    return retval;
}
function walk_the_kids(doc,children,variables,variable_values){
    for(k=0;k<children.length;k++){
        child=children[k];
        if(child.nodeType==1&&hasAttribute(child,'id')&&child.getAttribute('id')!=child.getAttribute('name')&&child.tagName!='LI'&&(child.tagName!='DIV'||child.style.display!='NONE')){
            var id=child.getAttribute('id');
            for(n=0;n<variables.length;n++){
                var reg=new RegExp('^'+variables[n]+'[0-9]+$');
                if(reg.test(id)){
                    if(child.tagName=='SELECT'){
                        var select=lookup_item(id,document);
                        variable_values[variables[n]]=select.options[select.selectedIndex].value;
                    }else{
                        variable_values[variables[n]]=lookup_item(id,document).value;
                    }
                }
            if(child.childNodes.length>0&&child.tagName!='OPTION'&&child.tagName!='TEXTAREA'){
                variable_values=walk_the_kids(doc,child.childNodes,variables,variable_values);
            }
            }
        }
    }
return variable_values;
}
var warned=false;
function calculate_formula(doc,formula,table_id){
    var total=0.00;
    var formula_type='';
    if(formula!='discount_amount'&&formula!='tax'){
        var variables=formula.match(/(_var_[a-zA-Z\_]+)+/g);
        var variable_values=new Array();
        formula=formula.replace(/(_var_)/g,'');
        for(q=0;q<variables.length;q++){
            variables[q]=trim(variables[q]).replace(/(_var_)/g,'');
        }
        }else
{
    formula_type=formula;
}
var table=doc.getElementById(table_id);
var rows=table.rows;
for(var i=0;i<rows.length;i++){
    if(formula_type=='discount_amount'){
        formula="unformatNumber('_var_discount_amount_', num_grp_sep, dec_sep) / 100 "
        +"* unformatNumber('_var_discount_price_', num_grp_sep, dec_sep) "
        +"* unformatNumber('_var_quantity_', num_grp_sep, dec_sep) * 1.0";
        if(i!=0){
            var chckd=null;
            var ckId='checkbox_select_'+table_array[table_id][i];
            if(typeof(rows[i].cells[11])!='undefined'&&typeof(rows[i].cells[11].childNodes[0])!='undefined'&&typeof(rows[i].cells[11].childNodes[0].childNodes[0])!='undefined'){
                chckd=rows[i].cells[11].childNodes[0].childNodes[0].checked;
            }else{
                chckd=doc.getElementById(ckId)&&doc.getElementById(ckId).checked;
            }
            if(chckd){
                formula="unformatNumber('_var_discount_amount_', num_grp_sep, dec_sep) / 100 "+"* unformatNumber('_var_discount_price_', num_grp_sep, dec_sep) "+"* unformatNumber('_var_quantity_', num_grp_sep, dec_sep) * 1.0";
            }
            else{
                formula="unformatNumber('_var_discount_amount_', num_grp_sep, dec_sep) "+"* unformatNumber('_var_quantity_', num_grp_sep, dec_sep) * 1.0";
            }
        }
    var variables=formula.match(/(_var_[a-zA-Z\_]+)+/g);
    var variable_values=new Array();
    formula=formula.replace(/(_var_)/g,'');
}
if(formula_type=='tax')
{
    var taxrate_value="taxrate_value";
    var taxrate=0.00;
    if(doc.EditView.taxrate_id.options.selectedIndex>-1){
        taxrate=get_taxrate(doc.EditView.taxrate_id.options[doc.EditView.taxrate_id.options.selectedIndex].value);
    }
    var taxable=SUGAR.language.get('app_list_strings','tax_class_dom');
    taxable=taxable['Taxable'];
    var formula_discount="unformatNumber('_var_discount_amount_', num_grp_sep, dec_sep) / 100 * unformatNumber('_var_discount_price_', num_grp_sep, dec_sep) * unformatNumber('_var_quantity_', num_grp_sep, dec_sep) * 1.0";
    if(i!=0){
        var chckd=null;
        var ckId='checkbox_select_'+table_array[table_id][i];
        if(typeof(rows[i].cells[11])!='undefined'&&typeof(rows[i].cells[11].childNodes[0])!='undefined'&&typeof(rows[i].cells[11].childNodes[0].childNodes[0])!='undefined'){
            chckd=rows[i].cells[11].childNodes[0].childNodes[0].checked;
        }else{
            chckd=doc.getElementById(ckId)&&doc.getElementById(ckId).checked;
        }
        if(chckd){
            formula_discount="unformatNumber('_var_discount_amount_', num_grp_sep, dec_sep) / 100 * unformatNumber('_var_discount_price_', num_grp_sep, dec_sep) * unformatNumber('_var_quantity_', num_grp_sep, dec_sep) * 1.0";
        }
        else{
            formula_discount="unformatNumber('_var_discount_amount_', num_grp_sep, dec_sep) * unformatNumber('_var_quantity_', num_grp_sep, dec_sep) * 1.0";
        }
    }
formula="(unformatNumber('_var_discount_price_', num_grp_sep, dec_sep) * unformatNumber('_var_quantity_', num_grp_sep, dec_sep) * 1.0 - "+formula_discount+") * "+taxrate+" * 1.0 * (('_var_tax_class_' == "+"'"+taxable+"') || ('_var_tax_class_' == "+"'Taxable'))";
var variables=formula.match(/(_var_[a-zA-Z\_]+)+/g);
    var variable_values=new Array();
    formula=formula.replace(/(_var_)/g,'');
}
for(q=0;q<variables.length;q++){
    variables[q]=trim(variables[q]).replace(/(_var_)/g,'');
}
cells=rows[i].cells;
for(q=0;q<variables.length;q++){
    variable_values[variables[q]]=0;
}
for(j=0;j<cells.length;j++){
    cell=rows[i].cells[j];
    children=cell.childNodes;
    if(typeof(cell.childNodes)=='undefined'||cell.childNodes==null)

    {
        continue;
    }
    variable_values=walk_the_kids(doc,children,variables,variable_values);
}
var newformula=formula;
for(z=0;z<variables.length;z++){
    var reg=variables[z];
    newformula=newformula.replace(reg,variable_values[variables[z]]);
}
try{
    total=total+eval(newformula);
}catch(exception){
    if(!warned){
        alert(check_data);
        warned=true;
    }
    return 0;
}
}
return total;
}
function calculate_subtotal(doc,table_id){
    var subtotal=0.00;
    subtotal=calculate_formula(doc,"unformatNumber('_var_discount_price_', num_grp_sep, dec_sep) * unformatNumber('_var_quantity_', num_grp_sep, dec_sep) * 1.0",table_id);
    lookup_item('subtotal_'+table_id,doc).value=formatNumber(toDecimal(subtotal,precision),num_grp_sep,dec_sep,precision,precision);
    return subtotal;
}
function calculate_discount(doc,table_id){
    var discount=0.00;
    discount=calculate_formula(doc,"discount_amount",table_id);
    lookup_item('deal_tot_'+table_id,doc).value=formatNumber(toDecimal(discount,precision),num_grp_sep,dec_sep,precision,precision);
    return discount;
}
function calculate_new_sub(doc,table_id){
    var new_sub=0.00;
    new_sub=unformatNumber(lookup_item('subtotal_'+table_id,doc).value,num_grp_sep,dec_sep)-unformatNumber(lookup_item('deal_tot_'+table_id,doc).value,num_grp_sep,dec_sep);
    lookup_item('new_sub_'+table_id,doc).value=formatNumber(toDecimal(new_sub,precision),num_grp_sep,dec_sep,precision,precision);
    return new_sub;
}
function calculate_tax(doc,table_id){
    var tax=0.00;
    tax=calculate_formula(doc,'tax',table_id);
    lookup_item('tax_'+table_id,doc).value=formatNumber(toDecimal(tax,precision),num_grp_sep,dec_sep,precision,precision);
    return tax;
}
function calculate_total(doc,table_id){
    var total=0.00;
    var discount_price;
    var quantity;
    var delete_me;
    total+=unformatNumber(lookup_item('subtotal_'+table_id,doc).value,num_grp_sep,dec_sep)+
    unformatNumber(lookup_item('tax_'+table_id,doc).value,num_grp_sep,dec_sep)+
    unformatNumber(lookup_item('shipping_'+table_id,doc).value,num_grp_sep,dec_sep)-
    unformatNumber(lookup_item('deal_tot_'+table_id,doc).value,num_grp_sep,dec_sep);
    lookup_item('total_'+table_id,doc).value=formatNumber(toDecimal(total,precision),num_grp_sep,dec_sep,precision,precision);
    return total;
}
function ConvertItems(id){
    var items=new Array();
    for(y=0;y<count;y++){
        var discount_price=lookup_item("discount_price_"+y,document);
        var list_price=lookup_item("list_price_"+y,document);
        var cost_price=lookup_item("cost_price_"+y,document);
        if(discount_price!=null&&typeof(discount_price)!='undefined'){
            discount_price.value=unformatNumber(discount_price.value,num_grp_sep,dec_sep);
            list_price.value=unformatNumber(list_price.value,num_grp_sep,dec_sep);
            cost_price.value=unformatNumber(cost_price.value,num_grp_sep,dec_sep);
            items[items.length]=list_price;
            items[items.length]=cost_price;
            items[items.length]=discount_price;
        }
    }
ConvertRate(id,items);
for(y=0;y<count;y++){
    var discount_price=lookup_item("discount_price_"+y,document);
    var list_price=lookup_item("list_price_"+y,document);
    var cost_price=lookup_item("cost_price_"+y,document);
    if(discount_price!=null&&typeof(discount_price)!='undefined'){
        discount_price.value=formatNumber(discount_price.value,num_grp_sep,dec_sep,precision,precision);
        list_price.value=formatNumber(list_price.value,num_grp_sep,dec_sep,precision,precision);
        cost_price.value=formatNumber(cost_price.value,num_grp_sep,dec_sep,precision,precision);
    }
}
calculate(document);
}
function isAmount(amount){
    if(amount<0){
        the_amount=amount*-1;
    }
    else{
        the_amount=amount;
    }
    return isFloat(the_amount);
}
function isCustomGroupStage(value){
    var quote_dom=SUGAR.language.get('app_list_strings','in_total_group_stages');
    for(var v in quote_dom){
        if(value==v){
            return false;
        }
    }
return true;
}

table_array['line_items_table']=new Array();
rows_nb_per_group['line_items_table']=1;
table_array['inclusions_table']=new Array();
rows_nb_per_group['inclusions_table']=1;
table_array['exclusions_table']=new Array();
rows_nb_per_group['exclusions_table']=1;
table_array['alternates_table']=new Array();
rows_nb_per_group['alternates_table']=1;

//Tax Class - Edited by Hirak - added tax_class param
function addLineItemRow(id,product_type,quantity,show_qty,product_template_id,product_name,show_title,cost_price,show_cost_price,list_price,group_total,show_total,discount_price,in_hours,unit_price,pricing_factor,tax_amount,tax_per,mft_part_num,table_id,bundle_stage,bundle_name,bundle_shipping,product_description,show_desc,type_id,discount_amount,discount_select,deal_calc,shipping,markup_inper,pc_modify,unit_measure,unit_measure_name, tax_class)
{	
	table_array[table_id][rows_nb_per_group[table_id]]=parseFloat(count);    
    rows_nb_per_group[table_id]=rows_nb_per_group[table_id]+1;
    
	var table=document.getElementById(table_id);
	
	// Tax Class - Edited by Hirak 
	var sales_tax_flag = document.getElementById('sales_tax_flag').value;
	if(tax_amount == ''){
		tax_amount = 0.00;
	}
	if(tax_per == ''){
		tax_per = 0.00;
	}
	if(tax_class == 'null'){
		tax_class = '';
	}
	// Tax Class - Edited by Hirak 
	
	if(product_type=='line_items' || product_type=='alternates'){	
		
		//Create new row for advance option * show    
	    var row1=table.insertRow(table.rows.length);
	    var rowName1='adv_opt_row_'+count;    
	    row1.id=rowName1;
	    row1.tableId=table.id;
	    row1.count=count;
	    var cell1=row1.insertCell(row1.cells.length);
	    var cell2=row1.insertCell(row1.cells.length);
	    var cell3=row1.insertCell(row1.cells.length);
	    var cell4=row1.insertCell(row1.cells.length);
	    var cell5=row1.insertCell(row1.cells.length);
	    var cell6=row1.insertCell(row1.cells.length);
	    var cell7=row1.insertCell(row1.cells.length);    
	    cell7.align="right";    
	    var cell7Div=document.createElement('div');
	    cell7Div.setAttribute('id', 'adv_opt_div_'+count);
	    cell7Div.style.height='5px';
	    cell7Div.style.color='#FF0000';
	    cell7Div.style.fontWeight='bold';
	    cell7Div.style.fontSize = '16px';
	    cell7Div.style.display='none';    
	    cell7Div.innerHTML='*';
	    cell7.appendChild(cell7Div);
	    
	    var cell8=row1.insertCell(row1.cells.length);
	    cell1.nowrap='nowrap';
	    
	    // Tax Class - Edited by Hirak 
    	if(sales_tax_flag == 'total_item'){
    		if(tax_class != 'Non-Taxable' || discount_amount > 0 || shipping > 0){
    			document.getElementById('adv_opt_div_'+count).style.display='';
    		}
    	}else{
    		if(tax_per > 0 || discount_amount > 0 || shipping > 0){
    			document.getElementById('adv_opt_div_'+count).style.display='';
    		}
    	}
    	// Tax Class - Edited by Hirak 
    
	}
	
	var row=table.insertRow(table.rows.length);
    var rowName='line_item_row_'+count;    
    row.setAttribute('valign','top');
    row.id=rowName;
    row.tableId=table.id;
    row.count=count;
    var cell=row.insertCell(row.cells.length);    
    cell.nowrap='nowrap';
    
    //Create Edit Button
    var buttonEl=document.createElement('input');
    buttonEl.setAttribute('type','button');
    var edit_button_id = 'btn_edit_'+count;
    buttonEl.count=count;
    buttonEl.onclick=function(){
    	var disableProductCatalog = jQuery("#disable_product_catalog").val();
    	if(trim($('#product_template_id_'+this.count).val()) == '' && disableProductCatalog == '1') {
    		showProductTemplatePopUp(this.count,product_type,edit_button_id);
    	} else {
    		showAddItemDiv(this.count,product_type,edit_button_id);
    	}    	
    }    
    buttonEl.setAttribute('name',"Edit["+count+"]");
    buttonEl.setAttribute('id',edit_button_id);
    buttonEl.setAttribute('value','Edit');
    buttonEl.className='button';
    cell.appendChild(buttonEl);
    
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"product_id["+count+"]");
    textEl.setAttribute('id',"product_id_"+count);
    textEl.setAttribute('value',id);
    item_list_MSI["product_id["+count+"]"]=textEl;
    cell.appendChild(textEl);
    
    //Product Type
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"product_type["+count+"]");
    textEl.setAttribute('id',"product_type_"+count);
    textEl.setAttribute('value',product_type);
    item_list_MSI["product_type["+count+"]"]=textEl;
    cell.appendChild(textEl);   
    
    //Product Template Id
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"product_template_id["+count+"]");
    textEl.setAttribute('id',"product_template_id_"+count);
    textEl.setAttribute('value',product_template_id);
    item_list_MSI["product_template_id["+count+"]"]=textEl;
    cell.appendChild(textEl);
    
    //In Hours
    if(product_type=='line_items' || product_type=='alternates'){
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"in_hours_li["+count+"]");
	    textEl.setAttribute('id',"in_hours_li_"+count);
	    textEl.setAttribute('value',in_hours);
	    item_list_MSI["in_hours_li_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
    }
       
    
    // Tax Class - Edited by Hirak 
    if(product_type=='line_items' || product_type=='alternates'){
    	
    	if(sales_tax_flag == 'total_item'){
    		
    	    var textEl=document.createElement('input');
    	    textEl.setAttribute('type','hidden')
    	    textEl.setAttribute('name',"tax_class["+count+"]");
    	    textEl.setAttribute('id',"tax_class_"+count);
    	    textEl.setAttribute('value',tax_class);
    	    item_list_MSI["tax_class_["+count+"]"]=textEl;
    	    cell.appendChild(textEl);
    	    
    	}else{
    		
    		var textEl=document.createElement('input');
		    textEl.setAttribute('type','hidden')
		    textEl.setAttribute('name',"bb_tax["+count+"]");
		    textEl.setAttribute('id',"bb_tax_"+count);
		    textEl.setAttribute('value',tax_amount);
		    item_list_MSI["bb_tax_["+count+"]"]=textEl;
		    cell.appendChild(textEl);
		    
		    var textEl=document.createElement('input');
		    textEl.setAttribute('type','hidden')
		    textEl.setAttribute('name',"bb_tax_per["+count+"]");
		    textEl.setAttribute('id',"bb_tax_per_"+count);
		    textEl.setAttribute('value',tax_per);
		    item_list_MSI["bb_tax_per_["+count+"]"]=textEl;
		    cell.appendChild(textEl);
    	}
    }
    // Tax Class - Edited by Hirak 
    
    //Shipping 
    if(product_type=='line_items' || product_type=='alternates'){
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"bb_shipping["+count+"]");
	    textEl.setAttribute('id',"bb_shipping_"+count);
	    textEl.setAttribute('value',shipping);
	    item_list_MSI["bb_shipping_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
    }
    
    //Discount Price
    if(product_type=='line_items' || product_type=='alternates'){
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"discount_price["+count+"]");
	    textEl.setAttribute('id',"discount_price_"+count);
	    textEl.setAttribute('value',discount_price);
	    item_list_MSI["discount_price_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
    }
    
    //Discount Amount
    if(product_type=='line_items' || product_type=='alternates'){
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"discount_amount["+count+"]");
	    textEl.setAttribute('id',"discount_amount_"+count);
	    textEl.setAttribute('value',discount_amount);
	    item_list_MSI["discount_amount_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
    }
    
    //Discount Select
    if(product_type=='line_items' || product_type=='alternates'){
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"discount_select["+count+"]");
	    textEl.setAttribute('id',"discount_select_"+count);
	    textEl.setAttribute('value',discount_select);
	    item_list_MSI["discount_select_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
    }
    
    //Markup Percentage Check
    if(product_type!='exclusions'){	   
    	var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"markup_inper["+count+"]");
	    textEl.setAttribute('id',"markup_inper_"+count);
	    textEl.setAttribute('value',markup_inper);
	    item_list_MSI["markup_inper_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
    }
    
    //Product Catalog Modified
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"pc_modify["+count+"]");
    textEl.setAttribute('id',"pc_modify_"+count);
    textEl.setAttribute('value',pc_modify);
    item_list_MSI["pc_modify_["+count+"]"]=textEl;
    cell.appendChild(textEl);
    
    //Create Product Name Field
    var cell=row.insertCell(row.cells.length);
    cell.width=200;
    cell.noWrap=true;
    var itemName='name_'+count;
	var textEl=document.createElement('input');
	textEl.setAttribute('type','text')
	textEl.size=30;	
	textEl.count=count;
	textEl.value=product_name;	
	textEl.setAttribute('name',"product_name["+count+"]");
	textEl.setAttribute('id',itemName);
	textEl.setAttribute('readOnly','readOnly');
	item_list_MSI[itemName]=textEl;
	cell.appendChild(textEl);
	
	//Show Title
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"title_show["+count+"]");
    textEl.setAttribute('id',"title_show_"+count);
    textEl.setAttribute('value',show_title);
    item_list_MSI["title_show_["+count+"]"]=textEl;
    cell.appendChild(textEl);

	//Create Description Field
    var pdesc_width=300;
    var pdesc_cols = 50;
    var desc_rows = 3;
    if(product_type=='inclusions'){
    	pdesc_width=500;
        pdesc_cols = 60;
        desc_rows = 5;
    }
    if(product_type=='exclusions'){
    	pdesc_width=800;
        pdesc_cols = 90;
    }
	var cell=row.insertCell(row.cells.length);
	//cell.width=pdesc_width;
	cell.noWrap=true;
	var itemName='description_'+count;
	var textEl=document.createElement('textarea');
	textEl.setAttribute('rows',desc_rows);
	textEl.setAttribute('cols',pdesc_cols);
	textEl.count=count;
	product_description=product_description.replace(/&#039;/g,'\'');
	textEl.value=product_description.replace(/<br>/g,'\n');
	textEl.alt=function(){
	    lookup_item(itemName,document);
	}
	textEl.setAttribute('name',"product_description["+count+"]");
	textEl.setAttribute('id',itemName);
	textEl.setAttribute('readOnly','readOnly');
	 if(product_type=='line_items' || product_type=='alternates')
	 {
	textEl.setAttribute('cols','35');
	textEl.setAttribute('rows','6');
	}
	item_list_MSI[itemName]=textEl;
	cell.appendChild(textEl);
	
	//Show Description
    var textEl=document.createElement('input');
    textEl.setAttribute('type','hidden')
    textEl.setAttribute('name',"desc_show["+count+"]");
    textEl.setAttribute('id',"desc_show_"+count);
    textEl.setAttribute('value',show_desc);
    item_list_MSI["desc_show_["+count+"]"]=textEl;
    cell.appendChild(textEl);
	
	//Create Quantity Field
    if(product_type=='line_items' || product_type=='alternates'){
	    
	    
	    var cell=row.insertCell(row.cells.length);
	    cell.nowrap='nowrap';   
	    cell.width=55;   
	    var textEl=document.createElement('input');    
	    textEl.setAttribute('type','text');
	    textEl.size=4;    
	    textEl.setAttribute('name',"quantity["+count+"]");
	    textEl.setAttribute('id',"quantity_"+count);
	    textEl.setAttribute('value',quantity);  
	    textEl.setAttribute('readOnly','readOnly');
	    item_list_MSI["quantity["+count+"]"]=textEl;
	    cell.appendChild(textEl);
	    
	    var cell=row.insertCell(row.cells.length);
	    cell.nowrap='nowrap';   
	    cell.width=55;  
	    
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"unit_measure["+count+"]");
	    textEl.setAttribute('id',"unit_measure_"+count);
	    textEl.setAttribute('value',unit_measure);
	    item_list_MSI["unit_measure_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
	    
	    var selEl=document.createElement('input');    
	    selEl.name ="unit_measure_name["+count+"]";
	    selEl.id = "unit_measure_name_"+count;
	    selEl.setAttribute('type','text');  	             
	    selEl.setAttribute('size','4');  	             
	    selEl.setAttribute('value',unit_measure_name);  	             
	    selEl.setAttribute('readonly','readonly');
	   /* for(k in unit_measure_dom){
			opt = new Option(k, unit_measure_dom[k]);
			if(unit_measure == k){
			opt.selected = true;}
			selEl.options[selEl.length] = opt;
			}*/
	    item_list_MSI["unit_measure_name["+count+"]"]=selEl;	    
	    cell.insertBefore(selEl,cell.nextSibling);
    }
    
    //Show Quantity
    if(product_type=='line_items' || product_type=='alternates'){
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"qty_show["+count+"]");
	    textEl.setAttribute('id',"qty_show_"+count);
	    textEl.setAttribute('value',show_qty);	    
	    item_list_MSI["qty_show_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
    }

    //Create Price Field
    if(product_type!='exclusions'){	    	    						
    	var cell=row.insertCell(row.cells.length);
		cell.width=75;
		var textEl=document.createElement('input');
		var itemName='cost_price_'+count;
		textEl.setAttribute('type','text')
		textEl.size=8;
		textEl.style.textAlign='right';	
		textEl.setAttribute('name',"cost_price["+count+"]");
		textEl.setAttribute('id',itemName);
		textEl.setAttribute('value',cost_price);
		textEl.setAttribute('readOnly','readOnly');
		item_list_MSI[itemName]=textEl;
		cell.appendChild(textEl);
    }
	
	//Show Price
    if(product_type!='exclusions'){
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"price_show["+count+"]");
	    textEl.setAttribute('id',"price_show_"+count);
	    textEl.setAttribute('value',show_cost_price);	    
	    item_list_MSI["price_show_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
    }   
	
	//Create Mark Up Field
    if(product_type!='exclusions'){
		var cell=row.insertCell(row.cells.length);
		cell.width=75;
		var textEl=document.createElement('input');
		var itemName='mark_up_'+count;
		textEl.setAttribute('type','text')
		textEl.size=8;
		textEl.style.textAlign='right';	
		textEl.setAttribute('name',"mark_up["+count+"]");
		textEl.setAttribute('id',itemName);
		textEl.setAttribute('value',list_price);
		textEl.setAttribute('readOnly','readOnly');
		item_list_MSI[itemName]=textEl;
		cell.appendChild(textEl);
    }
    
    
  //Unit Price
    if(product_type=='line_items' || product_type=='alternates'){
	    var cell=row.insertCell(row.cells.length);
	    cell.width=75;
	    var spanEl=document.createElement('span');	    
	    spanEl.setAttribute("id", "adv_opt_"+count);	    
    	var textEl=document.createElement('input');
	    textEl.setAttribute('type','text')
	    textEl.size=8;
	    textEl.style.textAlign='right';	
	    textEl.setAttribute('name',"unit_price["+count+"]");
	    textEl.setAttribute('id',"unit_price_"+count);
	    textEl.setAttribute('value',unit_price);
	    textEl.setAttribute('readOnly','readOnly');
	    item_list_MSI["unit_price_["+count+"]"]=textEl;	    
	    //cell.appendChild(spanEl);
	    cell.appendChild(textEl);
	    
    }
    
	
	//Create Total Field
	 if(product_type!='exclusions'){
		var cell=row.insertCell(row.cells.length);
		cell.width=75;
		var textEl=document.createElement('input');
		var itemName='group_total_'+count;
		textEl.setAttribute('type','text')
		textEl.size=8;
		textEl.style.textAlign='right';	
		textEl.setAttribute('name',"group_total["+count+"]");
		textEl.setAttribute('id',itemName);
		textEl.setAttribute('value',group_total);
		textEl.setAttribute('readOnly','readOnly');
		item_list_MSI[itemName]=textEl;
		cell.appendChild(textEl);
   
	
		//Show Total
	    var textEl=document.createElement('input');
	    textEl.setAttribute('type','hidden')
	    textEl.setAttribute('name',"total_show["+count+"]");
	    textEl.setAttribute('id',"total_show_"+count);
	    textEl.setAttribute('value',show_total);
	    item_list_MSI["total_show_["+count+"]"]=textEl;
	    cell.appendChild(textEl);
	 }
	
	//Create Hidden field for remove	
	var buttonEl=document.createElement('input');
	buttonEl.setAttribute('type','hidden');
	buttonEl.setAttribute('id','delete_row'+count);
	buttonEl.tableId=table_id;	
	buttonEl.count=count;	
	buttonEl.setAttribute('name',deleteButtonName);
	buttonEl.setAttribute('value',deleteButtonValue);	
	cell.appendChild(buttonEl);		
	count++;	
}

function deleteAdvOptionRow(id) {
	if (document.getElementById('adv_opt_div_' + id)) {
		YUI().use('node', 'transition', function(Y) {
			Y.one('#adv_opt_div_' + id).hide();
		});
	}
}


function deleteLineItemRow(id, table_id) {	
	deleteAdvOptionRow(id);
	for ( var i in table_array[table_id]) {
		if (table_array[table_id][i] == id) {
			table_array[table_id].splice(i, 1);
		}
	}
	var table = document.getElementById(table_id);
	var rows = table.rows;
	var looking_for = 'delete_row' + id;
	for (i = 1; i < rows.length; i++) {		
		cells = rows[i].cells;
		for ( var j = 2; j < rows[i].cells.length; j++) {
			cell = rows[i].cells[j];
			children = cell.childNodes;
			for ( var k = 0; k < children.length; k++) {
				var child = children[k];
				if (child.nodeType == 1 && hasAttribute(child, 'id')) {
					if (child.getAttribute('id') == looking_for) {
						table.deleteRow(i);
						return;
					}
				}
			}
		}
	}
}

function set_pro_return(popup_reply_data){
    var name_to_value_array = popup_reply_data.name_to_value_array;    
    var id = name_to_value_array['id'];
    //Modified by Mohit Kumar Gupta
    //for handling special character in name & description field
    //@date 22-oct-2013
    var name = $("<div/>").html(name_to_value_array['name']).text();
    var desc = $("<div/>").html(name_to_value_array['description']).text();    
    var cost_price = name_to_value_array['cost_price'];
    var unit_price = name_to_value_array['unit_price'];
    var markup = name_to_value_array['markup'];
    var quantity = name_to_value_array['quantity'];    
    var markup_inper = name_to_value_array['markup_inper'];
    var unit_measure = name_to_value_array['unit_measure'];
    
    //Tax Class - Added by Hirak
	if(document.getElementById('sales_tax_flag').value == 'total_item'){
		var tax_class = name_to_value_array['tax_class'];
		document.getElementById('pop_tax_class').value = (tax_class == '')?'Taxable':tax_class;
	}
	//Tax Class - Added by Hirak
    
    document.getElementById('product_tpl_name').value = name;
    document.getElementById('product_tpl_id').value = id;
    document.getElementById('pop_product_name').value = name;
    document.getElementById('pop_product_description').value = desc;    
    document.getElementById('pop_price').value = unformatNumber(cost_price, num_grp_sep, dec_sep);
    document.getElementById('pop_unit_price').value = unformatNumber(unit_price, num_grp_sep, dec_sep);
    document.getElementById('pop_markup').value = unformatNumber(markup, num_grp_sep, dec_sep);
    document.getElementById('pop_quantity').value = quantity;
    document.getElementById('pop_markup_inper').checked = false;
	if(unit_measure != ''){
    $('#pop_unit_measure option[value='+unit_measure+']').attr('selected',true);
	}
    if(markup_inper=='1'){
    	document.getElementById('pop_markup_inper').checked =true;
    }    	
    lineItemCalculate();
    getValueFromProductCatalog(id);
}

function getValueFromProductCatalog(pt_id){
	//Update modified value in Product Template.	
	YUI().use("io-base","node",function(Y){
		var uri = "index.php?module=AOS_Quotes&action=get_product_catalog&to_pdf=true&pt_id="+pt_id;
		var cfg = {
				method: 'POST',
				data: 'user=yahoo',
				headers: {
					'Content-Type':'application/json'
				},
				on: {
					start: function(){
						
					},
					complete:function(id,o){
						var res = JSON.parse(o.responseText);
						Y.one('#pre_pc_name').set('value',res['name']);
						Y.one('#pre_cost_price').set('value',res['cost_price']);
						Y.one('#pre_unit_price').set('value',res['unit_price']);
						Y.one('#pre_markup').set('value',res['markup']);
						Y.one('#pre_desc').set('value',res['desc']);
						Y.one('#pre_quantity').set('value',res['quantity']);
						Y.one('#pre_markup_inper').set('value',res['markup_inper']);						
						Y.one('#pre_unit_measure').set('value',res['unit_measure']);						
						disableUnitOfMeasure(res['quickbooks']);
					},
					end:function(){
						
					}
				}
		}
		var request = Y.io(uri,cfg);
	});
}

function saveProductCatalog(value){	
	var uri = "index.php?module=AOS_Quotes&action=save_product_catalog&to_pdf=true";
	$.ajax({
		url:uri,
		type : 'post',
		data:value,
		async:false,
		beforeSend:function(){},
		success:function(data){			
			document.getElementById('product_tpl_id').value = data;		
		},
		complete:function(){},
	});	
}

function showAddItemDiv(cnt,product_type,btn_name){
	document.getElementById('add_li_div').style.display='block';
	var btnX = YAHOO.util.Dom.getX(btn_name);
	var btnY = YAHOO.util.Dom.getY(btn_name);
	YAHOO.util.Dom.setX('add_li_div',btnX);
	YAHOO.util.Dom.setY('add_li_div',btnY+25);
	
	var sales_tax_flag = document.getElementById('sales_tax_flag').value;
	
	var type_name='';	
	var type_id='';	
	switch(product_type){
	case 'line_items':
	case 'alternates':	
		document.getElementById('tr_qty').style.display='';
		document.getElementById('tr_price').style.display='';
		document.getElementById('tr_adv_option').style.display='';
		//document.getElementById('tr_adv_opt_content').style.display='';
		document.getElementById('tr_hr2').style.display='';
		document.getElementById('tr_markup').style.display='';
		document.getElementById('tr_total').style.display='';		
		document.getElementById('price_label_span').style.display='none';
		document.getElementById('price_radio_span').style.display='';
		document.getElementById('pop_price').size=20;
		type_name='Line Items';
		type_id='3d300bbc-af00-3974-1a60-4ef4667d5755';
		break;	
	case 'inclusions':
		document.getElementById('tr_qty').style.display='none';
		document.getElementById('tr_price').style.display='';				
		document.getElementById('pop_price').size=30;
		document.getElementById('price_label_span').style.display='';
		document.getElementById('price_radio_span').style.display='none';
		document.getElementById('tr_adv_option').style.display='none';
		document.getElementById('tr_adv_opt_content').style.display='none';
		document.getElementById('tr_hr2').style.display='none';
		document.getElementById('tr_markup').style.display='';
		document.getElementById('tr_total').style.display='';
		document.getElementById('tr_unit_price').style.display='none';
		document.getElementById('pop_quantity').value='1';
		//document.getElementById('pop_price').value='0.00';
		//document.getElementById('pop_tax').value='0.00';
		//document.getElementById('pop_tax_amount').value='0.00';
		document.getElementById('pop_shipping').value='0.00';
		document.getElementById('pop_discount').value='0.00';
		document.getElementById('pop_discount_price').value='0.00'
		type_name='Inclusions';
		type_id='cd37aa0e-b135-68cf-010b-4f39eb63c652';
		break;
	case 'exclusions':
		document.getElementById('tr_qty').style.display='none';
		document.getElementById('tr_price').style.display='none';
		document.getElementById('tr_adv_option').style.display='none';
		document.getElementById('tr_adv_opt_content').style.display='none';
		document.getElementById('tr_hr2').style.display='none';
		document.getElementById('tr_markup').style.display='none';
		document.getElementById('tr_total').style.display='none';
		document.getElementById('tr_unit_price').style.display='none';
		type_name='Exclusions';
		type_id='6578f03c-4764-5fe1-cd63-4f39eb43f774';
		break;		
	}
	
	//sqs_objects['EditView_pop_product_name']['whereExtra'] = "type_name='"+type_name+"'";
	
	document.getElementById('btn_product').onclick=function(){	    
		var popup_request_data = {
	            'call_back_function':'set_pro_return',
	            'form_name':'EditView',
	            'field_to_name_array':{
	                'id':'id',
	                'name':'name',	                
	                'description':'description',
	                'cost_price':'cost_price',
	                'discount_price':'discount_price',
	                'markup':'markup',
	                'markup_inper':'markup_inper',
	                'quantity':'quantity',
	                'unit_measure' : 'unit_measure',
                	'unit_measure_name' : 'unit_measure_name',
                	'tax_class' : 'tax_class', // Tax Class - Added by Hirak
	            }            
	        };
		open_popup('AOS_ProductTemplates',600,400,'&query=true&type_id_advanced='+type_id,true,false,popup_request_data);	
		
	}	
	
	var table_name = product_type+'_table';
	
	var btnEdit = document.getElementById('btn_edit_'+cnt);
	if(btnEdit){	
		
		if(product_type=='line_items' || product_type=='alternates'){
			// Tax Class - Edited by Hirak
			if(sales_tax_flag == 'total_item'){
				if(document.getElementById('tax_class_'+cnt).value != '' || document.getElementById('bb_shipping_'+cnt).value > 0 || document.getElementById('discount_price_'+cnt).value > 0){
					document.getElementById('tr_adv_opt_content').style.display = "";
					document.getElementById("displayText").innerHTML = "<img src='themes/default/images/hide_submenu_shortcuts.gif' alt='hide_image'></a>";
				}else{
					document.getElementById('tr_adv_opt_content').style.display = "none";
					document.getElementById("displayText").innerHTML = "<img src='themes/default/images/show_submenu_shortcuts.gif' alt='show_image' border='0'></a>";
				}
			}else{
				if(document.getElementById('bb_tax_'+cnt).value > 0 || document.getElementById('bb_shipping_'+cnt).value > 0 || document.getElementById('discount_price_'+cnt).value > 0){
					document.getElementById('tr_adv_opt_content').style.display = "";
					document.getElementById("displayText").innerHTML = "<img src='themes/default/images/hide_submenu_shortcuts.gif' alt='hide_image'></a>";
				}else{
					document.getElementById('tr_adv_opt_content').style.display = "none";
					document.getElementById("displayText").innerHTML = "<img src='themes/default/images/show_submenu_shortcuts.gif' alt='show_image' border='0'></a>";
				}
			}
			// Tax Class - Edited by Hirak
		}		
		
		if(document.getElementById('name_'+cnt).value == 'This is Sample Text.' && trim(document.getElementById('product_template_id_'+cnt).value) == ''){
			document.getElementById('pop_product_name').value='';
			document.getElementById('pop_product_description').value='';
		}else{
			document.getElementById('pop_product_name').value=document.getElementById('name_'+cnt).value;
			document.getElementById('pop_product_description').value=document.getElementById('description_'+cnt).value;
		}
		if(document.getElementById('unit_measure_'+cnt)){
	     selOpt = $('#unit_measure_'+cnt).val();
		 if(selOpt != ''){
			$('#pop_unit_measure option[value='+selOpt+']').attr('selected',true);
		 }
	    }
		if(document.getElementById('quantity_'+cnt))
		document.getElementById('pop_quantity').value = document.getElementById('quantity_'+cnt).value;
		if(document.getElementById('cost_price_'+cnt))
		document.getElementById('pop_price').value = document.getElementById('cost_price_'+cnt).value;
		if(document.getElementById('mark_up_'+cnt))
		document.getElementById('pop_markup').value = document.getElementById('mark_up_'+cnt).value;
		if(document.getElementById('group_total_'+cnt))
		document.getElementById('pop_total').value = document.getElementById('group_total_'+cnt).value;
		
		// Tax Class - Added by Hirak
		if(sales_tax_flag == 'total_item'){
			if(document.getElementById('tax_class_'+cnt))
				document.getElementById('pop_tax_class').value = document.getElementById('tax_class_'+cnt).value;
		}else{
			if(document.getElementById('bb_tax_'+cnt))
				document.getElementById('pop_tax_amount').value = document.getElementById('bb_tax_'+cnt).value;
			if(document.getElementById('bb_tax_per_'+cnt))
				document.getElementById('pop_tax').value = document.getElementById('bb_tax_per_'+cnt).value;
		}
		// Tax Class - Added by Hirak
	
		if(document.getElementById('bb_shipping_'+cnt))
		document.getElementById('pop_shipping').value = document.getElementById('bb_shipping_'+cnt).value;
		if(document.getElementById('discount_price_'+cnt))
		document.getElementById('pop_discount_price').value = document.getElementById('discount_price_'+cnt).value;
		if(document.getElementById('discount_amount_'+cnt))
		document.getElementById('pop_discount').value = document.getElementById('discount_amount_'+cnt).value;
		if(document.getElementById('discount_select_'+cnt))
		(document.getElementById('discount_select_'+cnt).value==1)?document.getElementById('pop_disc_inper').checked=true:document.getElementById('pop_disc_inper').checked=false;
		
		if(document.getElementById('markup_inper_'+cnt))
		(document.getElementById('markup_inper_'+cnt).value==1)?document.getElementById('pop_markup_inper').checked=true:document.getElementById('pop_markup_inper').checked=false;
		
		
		if(document.getElementById('title_show_'+cnt))
		(document.getElementById('title_show_'+cnt).value==1)?document.getElementById('product_show').checked=true:document.getElementById('product_show').checked=false;
		if(document.getElementById('desc_show_'+cnt))
		(document.getElementById('desc_show_'+cnt).value==1)?document.getElementById('product_desc_show').checked=true:document.getElementById('product_desc_show').checked=false;
		if(document.getElementById('qty_show_'+cnt))
		(document.getElementById('qty_show_'+cnt).value==1)?document.getElementById('quantity_show').checked=true:document.getElementById('quantity_show').checked=false;
		if(document.getElementById('price_show_'+cnt))
		(document.getElementById('price_show_'+cnt).value==1)?document.getElementById('pop_price_show').checked=true:document.getElementById('pop_price_show').checked=false;
		if(document.getElementById('total_show_'+cnt))
		(document.getElementById('total_show_'+cnt).value==1)?document.getElementById('pop_total_show').checked=true:document.getElementById('pop_total_show').checked=false;
		if(document.getElementById('unit_price_'+cnt))
			document.getElementById('pop_unit_price').value = document.getElementById('unit_price_'+cnt).value;
		
		if(document.getElementById('product_template_id_'+cnt).value == ''){
			document.getElementById('product_tpl_id').value='';
			document.getElementById('product_tpl_name').value='';
		}else{		
			document.getElementById('product_tpl_id').value=document.getElementById('product_template_id_'+cnt).value;
			document.getElementById('product_tpl_name').value=document.getElementById('name_'+cnt).value;
		}
		
		if(document.getElementById('in_hours_li_'+cnt)){
			if(document.getElementById('in_hours_li_'+cnt).value==1){
				document.getElementById('radio_hours').checked=true;
				document.getElementById('radio_rate').checked=true;
			}else{
				document.getElementById('radio_qty').checked=true;
				document.getElementById('radio_price').checked=true;
			}
		}
		
		//Fetch Values from product template.
		if(document.getElementById('product_tpl_id').value != ''){
			getValueFromProductCatalog(document.getElementById('product_tpl_id').value);
		}
	}else{
		document.getElementById('pop_product_name').value='';
		document.getElementById('pop_product_description').value='';
		document.getElementById('pop_quantity').value='1';
		document.getElementById('pop_price').value='0.00';
		document.getElementById('pop_markup').value='0.00';
		document.getElementById('pop_total').value='0.00';
		// Tax Class - Added by Hirak
		if(sales_tax_flag == 'total_item'){
			document.getElementById('pop_tax_class').value = 'Taxable';
		}else{
			document.getElementById('pop_tax').value='0.00';
			document.getElementById('pop_tax_amount').value='0.00';
		}
		// Tax Class - Added by Hirak
		document.getElementById('pop_shipping').value='0.00';
		document.getElementById('pop_discount').value='0.00';
		document.getElementById('pop_discount_price').value='0.00';		
		document.getElementById('radio_qty').checked=true;
		document.getElementById('radio_price').checked=true;
		document.getElementById('pop_disc_inper').checked=false;
		document.getElementById('pop_markup_inper').checked=false;
		document.getElementById('product_show').checked=true;
		document.getElementById('product_desc_show').checked=true;
		document.getElementById('quantity_show').checked=true;
		document.getElementById('pop_price_show').checked=true;
		document.getElementById('pop_total_show').checked=true;
		document.getElementById('in_hours_hnd').value='';
		document.getElementById('product_tpl_id').value='';
		document.getElementById('product_tpl_name').value='';
		document.getElementById('pop_unit_price').value='0.00';
		$("#pop_unit_measure option:first").attr('selected','selected');
		document.getElementById('tr_adv_opt_content').style.display = "none";
		document.getElementById("displayText").innerHTML = "<img src='themes/default/images/show_submenu_shortcuts.gif' alt='show_image' border='0'></a>";
	}	
		
	document.getElementById('pop_save').onclick=function(){		
		var pname = document.getElementById('pop_product_name').value;
		var pdesc = document.getElementById('pop_product_description').value;
		var qty = 1;
		if(document.getElementById('pop_quantity').value != ''){
			qty = document.getElementById('pop_quantity').value;
		}
		var price = 0.00;
		if(document.getElementById('pop_price').value != '' && product_type!='exclusions'){
			price = parseFloat(document.getElementById('pop_price').value);
		}
		var markup = 0.00;
		if(document.getElementById('pop_markup').value != '' && product_type!='exclusions'){
			markup = parseFloat(document.getElementById('pop_markup').value);
		}		
		var group_total = document.getElementById('pop_total').value;
		var in_hours = document.getElementById('in_hours_hnd').value;		
		if(in_hours == ''){
		   in_hours =0;
		}
		
		// Tax Class - Added by Hirak
		if(sales_tax_flag == 'total_item'){
			var tax_amount = 0.00;
			var tax_per = 0.00;
			var tax_class = document.getElementById('pop_tax_class').value;
		}else{
			var tax_class = '';
			var tax_per = document.getElementById('pop_tax').value;
			var tax_amount = parseFloat(document.getElementById('pop_tax_amount').value);
		}		
		// Tax Class - Added by Hirak
		
		var product_show = 0;		
		if(document.getElementById('product_show').checked==true){
			product_show = 1;
		}
		
		var product_desc_show = 0;
		if(document.getElementById('product_desc_show').checked==true){
			product_desc_show=1;
		}
		
		var quantity_show = 0;
		if(document.getElementById('quantity_show').checked==true){
			quantity_show = 1;
		}
		
		var price_show = 0;
		if(document.getElementById('pop_price_show').checked==true){
			price_show = 1;
		}
		
		var total_show = 0;
		if(document.getElementById('pop_total_show').checked==true){
			total_show = 1;
		}
		
		var pop_discount_select = 0;
		if(document.getElementById('pop_disc_inper').checked==true){
			pop_discount_select = 1;
		}
		
		var pop_markup_inper = 0; 
		if(document.getElementById('pop_markup_inper').checked==true  && product_type!='exclusions'){
			pop_markup_inper = 1;
		}
			
		var pop_shipping = document.getElementById('pop_shipping').value;
		var pop_discount = document.getElementById('pop_discount').value;
		var pop_discount_price = parseFloat(document.getElementById('pop_discount_price').value);
		
		var pop_unit_price = 0.00;
		if(product_type!='exclusions') {
			pop_unit_price = parseFloat(document.getElementById('pop_unit_price').value);
		}
		
		//Get All pre defined value of product template
		var pre_cost_price = parseFloat(document.getElementById('pre_cost_price').value);
		var pre_unit_price = parseFloat(document.getElementById('pre_unit_price').value);
		var pre_markup = parseFloat(document.getElementById('pre_markup').value);
		var pre_markup_inper = parseFloat(document.getElementById('pre_markup_inper').value);
		var pre_desc = document.getElementById('pre_desc').value;
		var pre_quantity = document.getElementById('pre_quantity').value;
		var pre_unit_measure = $('#pre_unit_measure').val();
		//Modified by Mohit Kumar Gupta
	    //for handling pre_unit_measure undefined value for selecting existing line items
	    //@date 29-oct-2013
		if (typeof(pre_unit_measure)=='undefined') {
			pre_unit_measure = '';
		}
		var pre_pc_name = document.getElementById('pre_pc_name').value;
		var pre_pc_name = $("<div/>").html(pre_pc_name).text();
		var unit_measure = '';
		var unit_measure_name = '';
		if(product_type=='line_items' || product_type=='alternates') {
			unit_measure = $('#pop_unit_measure').val();
			unit_measure_name = $('#pop_unit_measure option:selected').text();
		}		
		
		var pc_modify = 1;		
		
		if(product_type=='line_items' || product_type=='alternates'){
			if(pre_cost_price == price && pre_unit_price == pop_unit_price 
			   && pre_markup == markup && pre_desc == pdesc && pre_quantity == qty 
			   && pop_markup_inper==pre_markup_inper && unit_measure == pre_unit_measure){
				pc_modify = 0;
			}
		}
		if(product_type=='inclusions'){
			if(pre_cost_price == price && pre_markup == markup && pre_desc == pdesc && pop_markup_inper==pre_markup_inper){
				pc_modify = 0;
			}
		}
		
		if(product_type=='exclusions'){
			if(pre_desc == pdesc){
				pc_modify = 0;
			}
		}
		var disableProductCatalog = jQuery("#disable_product_catalog").val();
		if(document.getElementById('product_tpl_id').value != ''){
			
			if(pc_modify == 1 && pname == pre_pc_name) {				
				if(disableProductCatalog == '1'){
					pc_modify=0;
				} else {
					var cnf = confirm('Do you want to change the values of that product in the catalog?');
					if(!cnf){
						pc_modify=0;
					}
				}				
			}
		}
		
		if(pname!=pre_pc_name){
			pc_modify=0;
		}
		
		var prod_tpl_id = document.getElementById('product_tpl_id').value;
		
		//Save Product Catalog
		if (pc_modify == 1 || prod_tpl_id == '' || (pname!=pre_pc_name && prod_tpl_id!='')) {
			if(pname!=pre_pc_name && prod_tpl_id!=''){				
				prod_tpl_id = '';				
			}	
			//Modified by Mohit Kumar Gupta
		    //for url encode pname,pdesc for special character "&" and others
		    //@date 29-oct-2013
			var pt_value = '&product_template_id=' + prod_tpl_id + '&ptype='
					+ product_type + '&pname=' + encodeURIComponent(pname) + '&pdesc=' + encodeURIComponent(pdesc)
					+ '&quantity=' + qty+'&unit_measure='+unit_measure + '&price=' + price + '&markup='
					+ markup + '&markup_inper=' + pop_markup_inper
					+ '&unit_price=' + pop_unit_price;	
                        if(sales_tax_flag == 'total_item'){
                           pt_value += '&tax_class='+$('#pop_tax_class').val(); 
                        }
			//Modified by Mohit Kumar Gupta
		    //allow product catalog create new or update old only when product template permission enable
		    //@date 21-04-2013
			if(disableProductCatalog == '0'){
				saveProductCatalog(pt_value);
			}
			
		}
		
		var prod_tpl_id = document.getElementById('product_tpl_id').value;		
		
		if(btnEdit){			
			document.getElementById('name_'+cnt).value= pname;
			document.getElementById('description_'+cnt).value = pdesc;
			if(document.getElementById('quantity_'+cnt))
			document.getElementById('quantity_'+cnt).value=qty;
			
			if(document.getElementById('unit_measure_'+cnt))
			{
				document.getElementById('unit_measure_'+cnt).value=unit_measure;
			}
			if(document.getElementById('unit_measure_name_'+cnt))
			{
				document.getElementById('unit_measure_name_'+cnt).value=unit_measure_name;
			}
			if(document.getElementById('cost_price_'+cnt))
			document.getElementById('cost_price_'+cnt).value=price.toFixed(2);
			if(document.getElementById('mark_up_'+cnt))
			document.getElementById('mark_up_'+cnt).value = markup.toFixed(2);;
			if(document.getElementById('group_total_'+cnt))
			document.getElementById('group_total_'+cnt).value= group_total;
			
			// Tax Class - Added by Hirak
			if(sales_tax_flag == 'total_item'){
				if(document.getElementById('tax_class_'+cnt))
					document.getElementById('tax_class_'+cnt).value = tax_class;
			}else{
				if(document.getElementById('bb_tax_'+cnt))
					document.getElementById('bb_tax_'+cnt).value = tax_amount.toFixed(2);
				if(document.getElementById('bb_tax_per_'+cnt))
					document.getElementById('bb_tax_per_'+cnt).value = tax_per;
			}
			// Tax Class - Added by Hirak

			
			if(document.getElementById('bb_shipping_'+cnt))
			document.getElementById('bb_shipping_'+cnt).value = pop_shipping;
			if(document.getElementById('discount_price_'+cnt))
			document.getElementById('discount_price_'+cnt).value = pop_discount_price.toFixed(2);
			if(document.getElementById('discount_amount_'+cnt))
			document.getElementById('discount_amount_'+cnt).value = pop_discount;
			
			if(document.getElementById('discount_select_'+cnt))
			document.getElementById('discount_select_'+cnt).value = pop_discount_select;
			
			if(document.getElementById('markup_inper_'+cnt))
			document.getElementById('markup_inper_'+cnt).value = pop_markup_inper;
			
			if(document.getElementById('title_show_'+cnt))
			document.getElementById('title_show_'+cnt).value = product_show;
			if(document.getElementById('desc_show_'+cnt))
			document.getElementById('desc_show_'+cnt).value = product_desc_show;
			if(document.getElementById('qty_show_'+cnt))
			document.getElementById('qty_show_'+cnt).value = quantity_show;
			if(document.getElementById('price_show_'+cnt))
			document.getElementById('price_show_'+cnt).value = price_show;
			if(document.getElementById('total_show_'+cnt))
			document.getElementById('total_show_'+cnt).value = total_show;
			if(document.getElementById('in_hours_li_'+cnt))
			document.getElementById('in_hours_li_'+cnt).value = in_hours;			
			document.getElementById('product_template_id_'+cnt).value = prod_tpl_id;
			if(document.getElementById('unit_price_'+cnt))
			document.getElementById('unit_price_'+cnt).value = pop_unit_price.toFixed(2);;		
			
			
			document.getElementById('pc_modify_'+cnt).value = pc_modify;
			
			if(product_type=='line_items' || product_type=='alternates'){
				lineItemCalculate();
			}
			calculateSubTotal();
			
			//Display * on Unit Price if Advance options is selected		
			if(product_type == 'line_items' || product_type=='alternates'){
				//Tax Class - Edited by Hirak
				document.getElementById('adv_opt_div_'+cnt).style.display='none';
				if(sales_tax_flag == 'total_item'){
					if(document.getElementById('pop_tax_class').value != 'Non-Taxable' || document.getElementById('pop_shipping').value > 0 || document.getElementById('pop_discount').value > 0){	
						document.getElementById('adv_opt_div_'+cnt).style.display='';
					}
				}else{
					if(document.getElementById('pop_tax').value > 0 || document.getElementById('pop_shipping').value > 0 || document.getElementById('pop_discount').value > 0){	
						document.getElementById('adv_opt_div_'+cnt).style.display='';
					}
				}
				//Tax Class - Edited by Hirak
			}
		}else{
			//Tax Class - Edited by Hirak
			addLineItemRow('',product_type,qty,quantity_show,prod_tpl_id,pname,product_show,price,price_show,markup,group_total,total_show,pop_discount_price,in_hours,pop_unit_price,'',tax_amount,tax_per,'',table_name,'','','',pdesc,product_desc_show,'',pop_discount,pop_discount_select,'',pop_shipping,pop_markup_inper,pc_modify,unit_measure,unit_measure_name, tax_class);
			if(product_type=='line_items' || product_type=='alternates'){
				lineItemCalculate();
			}
			calculateSubTotal();
		}
		
		document.getElementById('add_li_div').style.display='none';
				
	}
	
	document.getElementById('pop_cancel').onclick=function(){
		document.getElementById('add_li_div').style.display='none';
		//Shashank - Enable Dropdown if it was disabled Previously due to Master Quickbook Setting
		$('#pop_unit_measure').prop("disabled", false);
	}
	

	document.getElementById('pop_delete').onclick=function(){		
		if(document.getElementById('delete_row'+cnt)){
			if(confirm('Are you sure you want to remove this row from the proposal?')){    	
				//Add delete product id in deleted array
				var prod_id = document.getElementById('product_id_'+cnt).value;
				var form=document.getElementById('EditView');
				var textEl=document.createElement('input');
			    textEl.setAttribute('type','hidden')
			    textEl.setAttribute('name',"deleted_prod_id["+cnt+"]");
			    textEl.setAttribute('id',"deleted_prod_id_"+cnt);
			    textEl.setAttribute('value',prod_id);		    
			    form.appendChild(textEl);
			  //End delete product id in deleted array
				deleteLineItemRow(cnt,table_name);
				lineItemCalculate();			
				calculateSubTotal();
				document.getElementById('add_li_div').style.display='none';		    	
		    }
		}
	}	
	
	
}


function toggle() {
	var ele = document.getElementById("tr_adv_opt_content");
	var text = document.getElementById("displayText");
	if(ele.style.display == "") {
    		ele.style.display = "none";
    		text.innerHTML = "<img src='themes/default/images/show_submenu_shortcuts.gif' alt='show_image' border='0'></a>";	
  	}else {
		ele.style.display = "";
		text.innerHTML = "<img src='themes/default/images/hide_submenu_shortcuts.gif' alt='hide_image' border='0'></a>";			
	}
} 



function lineItemCalculate(){
	var subTotalValue=0.00;
	var totalValue = 0.00;	
	var pop_qty = 1;	
	
	var sales_tax_flag = document.getElementById('sales_tax_flag').value; //Tax Class - Added by Hirak
	
	if(document.getElementById('pop_quantity')){
		if(document.getElementById('pop_quantity').value != ''){
			pop_qty = parseFloat(document.getElementById('pop_quantity').value);
		}
	}
	var pop_price = 0.00;
	if(document.getElementById('pop_price')){
		if(document.getElementById('pop_price').value != ''){
			pop_price = parseFloat(document.getElementById('pop_price').value);
		}
	}
	var pop_markup = 0.00;
	if(document.getElementById('pop_markup')){
		if(document.getElementById('pop_markup').value != ''){
			pop_markup = parseFloat(document.getElementById('pop_markup').value);
		}
	}
	var pop_discount = 0.00;
	if(document.getElementById('pop_discount')){
		if(document.getElementById('pop_discount').value != ''){
			pop_discount = parseFloat(document.getElementById('pop_discount').value);
		}
	}
	
	
	//Get SubTotal
	//alert(pop_qty);
	//alert(pop_price);
	//alert(pop_markup);
	subTotalValue = pop_qty*pop_price;	
	//alert(subTotalValue);
	
	if(document.getElementById('pop_markup_inper').checked==true){
		pop_markup = (subTotalValue*pop_markup)/100;
	}
		
	subTotalValue = subTotalValue + pop_markup;	
	
	//Add Unit Price
	var unit_price=0.00;
	if(pop_qty > 0){
		unit_price = subTotalValue/pop_qty;
	}
	
	document.getElementById('pop_unit_price').value=unit_price.toFixed(2);
	
	
	var discount = pop_discount;
	if(document.getElementById('pop_disc_inper').checked==true){		
		discount = (subTotalValue*pop_discount)/100;
	}	
		
	document.getElementById('pop_discount_price').value=discount;
	//Less discount
	subTotalValue = subTotalValue-discount;
	
	
	//Add Shipping
	var pop_shipping=0.00;
	if(document.getElementById('pop_shipping').value !=''){
		pop_shipping = parseFloat(document.getElementById('pop_shipping').value);
	}
	
	
	//Tax Class - Edited by Hirak
	if(sales_tax_flag == 'total_item'){
		totalValue = subTotalValue+pop_shipping
	}else{
		//Add Tax
		var pop_tax=0.00;
		if(document.getElementById('pop_tax').value!=''){
			pop_tax = parseFloat(document.getElementById('pop_tax').value);
		}
		
		var tax = (subTotalValue*pop_tax)/100;
		
		document.getElementById('pop_tax_amount').value=tax;
		totalValue = subTotalValue+tax+pop_shipping
	}
	//Tax Class - Edited by Hirak
	
	document.getElementById('pop_total').value = totalValue.toFixed(2);
}


function calculateSubTotal(){	
	var subtotal_li = 0;
	var subtotal_inc = 0;
	var total_tax = 0;
	var total_shipping = 0;	
	var sales_tax_flag = document.getElementById('sales_tax_flag').value; //Tax Class - Added by Hirak
	
	for(var i=0; i<count;i++){		
		if(document.getElementById('product_type_'+i)){
			if(document.getElementById('product_type_'+i).value=='line_items'){
				
				var total_li=0.00;
				if(document.getElementById('group_total_'+i).value!=''){
					var total_li = parseFloat(document.getElementById('group_total_'+i).value);
				}
				subtotal_li += total_li;
				
				var total_shipping_val = 0.00;
				if(document.getElementById('bb_shipping_'+i).value!=''){
					total_shipping_val = parseFloat(document.getElementById('bb_shipping_'+i).value);
				}
				total_shipping +=total_shipping_val;
				
				
				var total_tax_val = 0.00;
				
				//Tax Class - Edited by Hirak
				if(sales_tax_flag == 'total_item'){
					var tax_rate = document.getElementById('tax_rate').value;
					if(document.getElementById('tax_class_'+i).value == 'Taxable'){
						total_tax_val = (total_li*tax_rate)/100;
					}
				}else{
					if(document.getElementById('bb_tax_'+i).value!=''){
						total_tax_val = parseFloat(document.getElementById('bb_tax_'+i).value);
					}
				}
				total_tax +=total_tax_val;
				//Tax Class - Edited by Hirak
			}
		}
		
		if(document.getElementById('product_type_'+i)){
			if(document.getElementById('product_type_'+i).value=='inclusions'){
				var total_inc = 0.00;
				if(document.getElementById('group_total_'+i).value!=''){
					total_inc = parseFloat(document.getElementById('group_total_'+i).value);
				}
				subtotal_inc += total_inc;
			}
		}
	}
	
	//Tax Class - Edited by Hirak
	if(sales_tax_flag == 'total_item'){
		var grandTotal = subtotal_li+subtotal_inc+total_tax;
		var grandSubTotal = grandTotal-(total_tax+total_shipping);
	}else{
		var grandTotal = subtotal_li+subtotal_inc;
		var grandSubTotal = grandTotal-(total_tax+total_shipping);
	}
	//Tax Class - Edited by Hirak
	
	
	document.getElementById('subtotal_html').innerHTML = subtotal_li.toFixed(2);
	document.getElementById('subtotal').value = subtotal_li.toFixed(2);
	document.getElementById('subtotal_inc_html').innerHTML = subtotal_inc.toFixed(2);
	document.getElementById('subtotal_inc').value = subtotal_inc.toFixed(2);
	document.getElementById('grand_sub_div').innerHTML = grandSubTotal.toFixed(2);
	document.getElementById('grand_sub').value = grandSubTotal.toFixed(2);
	document.getElementById('grand_tax_div').innerHTML = total_tax.toFixed(2);
	document.getElementById('grand_tax').value = total_tax.toFixed(2);
	document.getElementById('grand_ship_div').innerHTML = total_shipping.toFixed(2);
	document.getElementById('grand_ship').value = total_shipping.toFixed(2);
	document.getElementById('grand_total_div').innerHTML = grandTotal.toFixed(2);
	document.getElementById('grand_total').value = grandTotal.toFixed(2);
	//if grad total gt than 0 make it Proposal Amount
	if(grandTotal > 0){
		document.getElementById('proposal_amount').innerHTML = grandTotal.toFixed(2);
		document.EditView.proposal_amount.value = grandTotal.toFixed(2);
	}else{
		var opportunity_amount = parseFloat(document.getElementById('opportunity_amount').value);
		document.getElementById('proposal_amount').innerHTML = opportunity_amount.toFixed(2);
		document.EditView.proposal_amount.value = opportunity_amount.toFixed(2);
	}
}
/**
 * use when product template is disabled
 * @author Mohit Kumar Gupta
 * @date 18-04-2014
 * @param cnt integer
 * @param product_type string
 * @param btn_name string
 */
function showProductTemplatePopUp(cnt,product_type,btn_name) {
	var popup_request_data = {
	    'call_back_function':'set_custom_pro_return',
	    'form_name':'EditView',
	    'field_to_name_array':{
	        'id':'id',
	        'name':'name',	                
	        'description':'description',
	        'cost_price':'cost_price',
	        'discount_price':'discount_price',
	        'markup':'markup',
	        'markup_inper':'markup_inper',
	        'quantity':'quantity',
	        'unit_measure' : 'unit_measure',
	        'tax_class' : 'tax_class', //Tax Class - Edited by Hirak
	    },
	    'passthru_data':{'cnt':cnt,'product_type':product_type,'btn_name':btn_name} 
			
    };
	open_popup('AOS_ProductTemplates',600,400,'&query=true&type_name='+product_type,true,false,popup_request_data);
}
/**
 * use when product template is disabled
 * and hold opo up response
 * @author Mohit Kumar Gupta
 * @date 18-04-2014
 * @param popup_reply_data
 */
function set_custom_pro_return(popup_reply_data) {
	var passthru_data = popup_reply_data.passthru_data;
	var cnt = passthru_data['cnt'];
	var product_type = passthru_data['product_type'];
	var btn_name = passthru_data['btn_name'];
	showAddItemDiv(cnt,product_type,btn_name);
	set_pro_return(popup_reply_data);	
}

/**
 * get the tax rate from the ID
 */
function chengeTaxRate(){
	 var taxrate_id = document.getElementById('taxrate_id').value;
	 var tax_rate = 0.00;
	 
	 if(taxrate_id == ''){
		 document.getElementById('tax_rate').value = tax_rate.toFixed(2);
		 document.getElementById('tax_rate_label').innerHTML = '('+tax_rate.toFixed(2)+' %)';
		 calculateSubTotal();
		 return false;
	 }
	 
	 ajaxStatus.showStatus('Loading ...');
	 jQuery.ajax({
        type: "POST",
        url: "index.php?module=AOS_Quotes&action=taxrate&to_pdf=true",
        data: {taxrate_id: taxrate_id},
       	cache: false,
       	async:true,
        complete: function (resp,data) {
       		aResp = JSON.parse(resp.responseText);
      		if( aResp.result = 'success' ){ 
      			tax_rate = parseFloat(aResp.tax_rate);
      			document.getElementById('tax_rate').value  = tax_rate.toFixed(2);
      			document.getElementById('tax_rate_label').innerHTML = '('+tax_rate.toFixed(2)+' %)';
      		}else{
      			document.getElementById('tax_rate').value  = tax_rate.toFixed(2);
      			document.getElementById('tax_rate_label').innerHTML = '('+tax_rate.toFixed(2)+' %)';
      		}
      		calculateSubTotal();
      		ajaxStatus.hideStatus();                    	
        }
     });
}
//commented the code due to change for QuickBooks Two Sync
//tax rate id updated on dit view of quotes
/*
$(document).ready(function(){
	var sales_tax_flag = document.getElementById('sales_tax_flag').value;
	if(sales_tax_flag == 'total_item'){
		chengeTaxRate();
	} 
})
*/
/*
 * Disable Unit of Measure if Existing Product Catalog is selected
 */
function disableUnitOfMeasure(quickbookVal){
	if(quickbookVal == 1){
		$('#pop_unit_measure').prop("disabled", true);
	}
}