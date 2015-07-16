<style>
.group_tr {background-color: #FDC}
</style>

<div>
    <div style='margin-bottom: 15px'>
        <?php echo Form::open($action, array('id'=>'tm-form', 'class'=>'form-horizontal', 'method'=>'get'))?>
            <?php echo Form::select('uri', $routes, $current, array('id' => 'api_select', 'class' => 'form-control', 'style'=>'width:300px')) ?>
        <?php echo Form::close() ?>
        <a onclick='delete_all()' class='btn btn-warning'>清空所有Optimization数据</a>
    </div>
</div>

<?php if ($current): ?>
<table class='table table-bordered'>
    <?php foreach ($uri_reports as $group_name => $group): ?>
        <tr class='group_tr'>
            <td colspan='6'>Group: <?php echo $group_name ?></td>
        </tr>
        <?php foreach ($group as $item_name => $item): ?>
            <tr>
                <td rowspan='2' width='40%'><div><?php echo $item_name ?></div></td>
                <td><div>最大时间</div><div><?php echo round($item['max_time'] * 1000, 3) ?>ms</div></td>
                <td><div>最小时间</div><div><?php echo round($item['min_time'] * 1000, 3) ?>ms</div></td>
                <td><div>总时间</div><div><?php echo round($item['total_time'] * 1000, 3) ?>ms</div></td>
                <td><div>平均时间</div><div><?php echo round($item['average_time'] * 1000, 3) ?>ms</div></td>
                <td><div>执行次数</div><div><?php echo $item['exe_times'] ?>次</div></td>
            </tr>
            <tr>
                <td><div>最大内存</div><div><?php echo ceil($item['max_memory']) ?>byte</div></td>
                <td><div>最小内存</div><div><?php echo ceil($item['min_memory']) ?>byte</div></td>
                <td><div>总内存</div><div><?php echo ceil($item['total_memory']) ?>byte</div></td>
                <td><div>平均内存</div><div><?php echo ceil($item['average_memory']) ?>byte</div></td>
                <td><div>更新时间</div><div><?php echo date('Y-m-d H:i:s', $item['updated_time']) ?></div></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>
<?php else: ?>
<div class="help">
    <p><?php echo __('You can look up execute time and memory consume of uri here.')?></p>
</div>

<h3>使用指引</h3>
<p>选择不同的URI来看你需要检查的链接</p>

<?php endif; ?>

<script>
(function($){
    $().ready(function(){
        $("#api_select").change(function(){
            $("#tm-form").submit();     
        });    
    }); 
})(jQuery);

function delete_all()
{
    if(confirm('你确定要删除完吗？'))
    {
        $.post('<?php echo URL::base() ?>optimizer/delete', {}, function(result){
            result = eval('(' + result + ')');
            alert(result['data']);
        });
    }
}
</script>
