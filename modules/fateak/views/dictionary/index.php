<style>
.group_tr {background-color: #FDC}
</style>

<div>
    <div style='margin-bottom: 15px'>
        <?php echo Form::open($action, array('id'=>'tm-form', 'class'=>'form-horizontal', 'method'=>'get'))?>
            <?php echo Form::select('dic', $dics, $current, array('id' => 'dic_select', 'class' => 'form-control', 'style'=>'width:300px')) ?>
        <?php echo Form::close() ?>
    </div>
</div>

<?php if ($current): ?>
<div style='margin-bottom:10px'>
    <a onclick='write_dict("<?php echo $current ?>")' class='btn btn-info'>生成词典</a>
</div>
<div style='margin-bottom:10px'>
    <a onclick='add_word("<?php echo $current ?>")' class='btn btn-warning'>在本词典添加新词</a>
    <input id='new_word' type='text' />
</div>
<table class='table table-hover'>
   <tr>
        <th>词</th>
        <th>词频(无用)</th>
        <th>词重(无用)</th>
        <th>词性(无用)</th>
   </tr>
   <?php foreach ($words as $group_id => $group): ?>
        <tr><th colspan='4' class='group_tr'>组别：<?php echo $group_id ?></th></tr>
        <?php foreach ($group as $word => $info): ?>
            <tr>
                <td><?php echo $word ?></td>
                <td><?php echo $info['tf'] ?></td>
                <td><?php echo $info['idf'] ?></td>
                <td><?php echo $info['attr'] ?></td>
            </tr>
        <?php endforeach; ?>
   <?php endforeach; ?>
</table>
<?php else: ?>
<div class="help">
    <p>默认的词典是一般不需要编辑的，我们通常按照模块建立所需加载的字典</p>
</div>

<h3>使用指引</h3>
<p>选择不同的字典路径来看你需要查阅编辑的字典</p>

<?php endif; ?>

<script>
(function($){
    $().ready(function(){
        $("#dic_select").change(function(){
            $("#tm-form").submit();     
        });    
    }); 
})(jQuery);

function add_word(module)
{
    $.post('<?php echo URL::base() ?>dictionary/add', {word: $("#new_word").val(), module:module}, function(result){
        result = eval('(' + result + ')');
        if (result['success'] == 'Y')
        {
            alert(result['data']);
        }
        else
        {
            alert(result['message']);
        }
    });
}

function write_dict(module)
{
    if(confirm('你确定要生成新字典吗？'))
    {
        $.post('<?php echo URL::base() ?>dictionary/write', {module: module}, function(result){
            result = eval('(' + result + ')');
            if (result['success'] == 'Y')
            {
                alert(result['data']);
            }
            else
            {
                alert(result['message']);
            }        
        });
    }
}
</script>
