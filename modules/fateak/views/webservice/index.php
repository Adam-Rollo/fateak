<style>
.apidocs .panel-body {padding: 5px 10px}
.apidocs .panel-body ul{padding-left: 0px}
.apidocs .panel-body li{list-style: none}
</style>

<div style='margin-bottom: 15px'>
    <?php echo Form::open($action, array('id'=>'docs-form', 'class'=>'form-horizontal', 'method'=>'get'))?>
        <?php echo Form::select('module', $modules, $current, array('id' => 'api_select', 'class' => 'form-control', 'style'=>'width:300px')) ?>
    <?php echo Form::close() ?>
</div>

<?php if ($current): ?>
    <pre>
<?php echo HTML::chars(trim($webservice_class_comment)) ?>
    </pre>
    <?php foreach ($webservice_functions as $function): ?>
        <div>
            <h4><?php echo $function['name'] ?></h4>
            <div class='label label-default' style='font-size:100%'><?php echo URL::base(true) . 'api/' . substr($function['class'], 11) . '/' . $function['function'] ?> </div>
            <pre style='background-color:white;border:none'><?php echo HTML::chars($function['comment']['comment']) ?></pre>
            <div class="apidocs panel panel-default">
                <div class="panel-heading"><?php echo $function['classfunction'] ?></div>
                <div class="panel-body">
                    <span class="label label-primary"><?php echo __('Parameters') ?></span>
                    <ul style='margin-bottom:0px;margin-top:10px'>
                        <?php foreach ($function['comment']['params'] as $param => $introduction): ?>
                            <li>
                                <span style='color:#126'><?php echo $param ?></span>
                                <?php echo " : ".$introduction ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <hr style="margin:1px 2px" />
                <div class="panel-body">
                    <span class="label label-success"><?php echo __('Return Value') ?></span>
                    <div style='margin-top:10px'>
                        <span style='color:#162'><?php echo $function['comment']['return']['type'] ?></span>
                        <?php echo $function['comment']['return']['info'] ?>
                    </div>
                </div>
            </div>
            <hr/>
        </div>
    <?php endforeach; ?>

<?php else: ?>
<div class="alert alert-success">
    <p><?php echo __('You can search any webservice API here . Just change the select container !')?></p>
    <p>RSA的密钥请在这里下载: <a href='<?php echo URL::base() ?>assets/data/public_key.txt' download='public_key.txt'>RSA公钥</a></p>
    <p>RSA加密过后的密文，请用basecode64转码，以便更好的在网络间传输</p>
    <p>请注意，某些系统basecode64转码时，并不会对空格（加号）做处理，请君注意处理并进行转码</p>
    <p>新版密码明文统一为{'username':'用户名','password':'用户密码','timestamp':'当前时间戳'}</p>
</div>

<h3>使用指引</h3>
<p>这里搜索出来的文档全部是PHP方法名及其注释, 下面是一个文档示例</p>

<div style='border:1px solid #BBB; padding: 20px; margin-bottom:10px'>
    <h4>get_page</h4>
    <p>得到相应ID的页面</p>
    <div class="apidocs panel panel-default">
        <div class="panel-heading">content/get_page</div>
        <div class="panel-body">
            <span class="label label-primary">参数</span>
            <ul style="margin-bottom:0px;margin-top:10px">
                <li>
                    <span style="color:#126">id</span>
                    : 页面的ID                            
                </li>
            </ul>
        </div>
        <hr style="margin:1px 2px">
        <div class="panel-body">
            <span class="label label-success">返回值</span>
            <div style='margin-top:10px'>
                <span style="color:#162">ORM</span>
                返回一个类型为页面的指定ID的页面                    
            </div>
        </div>
    </div>
</div>
<p><b>其中content是API的类名。比如Content类的名字则为小写content。Content_Blog类则为content/blog。</b></p>
<p><b>其中get_page是API的方法名。在每一个API中，这个名称是唯一的。</b></p>
<p>在API名称下面是这个API的简单介绍，它主要的作用和注意事项</p>
<p>content/get_page是这个API的调用方法，而后面的参数则是其调用时所需要填的参数</p>
<p>一般来说返回值必须是一个字符串或者数组，如果这里是一个对象或其它东西，也无需太过在意，最终都会转变成数组或者字符串。</p>
<p>综上所属，如果在模板中调用这个API，那么写法如下</p>
<pre>
<?php echo HTML::chars("{%v% page = content/get_page [id=1]}"); ?>
</pre>
<p>如果是手机端等其它客户端调用，则可以直接以POST的形式调用，代码如下（仍然是以PHP作为例子）</p>
<pre>
<?php echo HTML::chars("Curl::post('http://YOUR_DOMAIN/api/content/get_page', array('id' => 1));") ?>

</pre>
<?php endif; ?>

<script>
(function($){
    $().ready(function(){
        $("#api_select").change(function(){
            $("#docs-form").submit();     
        });    
    }); 
})(jQuery);
</script>
