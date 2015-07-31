<?php
/**
 * Fateak - Rollo
 * @uses SCWS
 */
class Controller_Dictionary extends Controller_Fate
{
    public function action_index()
    {
        // Page settings
        $this->title = "望不到尽头的辞海 ~ Tell You Nothing";
        $this->breadcrumb->addItem('Tell You Nothing');

        // init
        $redis = FRedis::instance();
        $current = $this->request->query('dic');

        // 按照模块来找到字典
        $modules = Module::modules();
        unset($modules['auth']);unset($modules['cache']);unset($modules['database']);unset($modules['image']);unset($modules['minion']);unset($modules['unittest']);unset($modules['captcha']);unset($modules['fateak']);
        $dic_modules = array('0' => __('Please select a module'));

        foreach ($modules as $name => $path)
        {
            if ($this->find_dic($name))
            {
                $dic_modules[$name] = $name;
            }
        }

        $current_module = $this->request->query('dic');
        $current_module = $current_module ? $current_module : '0';

        if ($current_module != '0')
        {
            $current_path = $this->find_dic($current_module);
            $txt = $current_path . 'dict.utf8.txt';
            $xdb = $current_path . 'dict.utf8.xdb';

            $fd = @fopen($txt, 'r');
            $total = 0;
            $rec = array();

            while ($line = fgets($fd, 512))
            {
                if (substr($line, 0, 1) == '#') continue;
                list($word, $tf, $idf, $attr) = explode("\t", $line, 4);
                $k = (ord($word[0]) + ord($word[1])) & 0x3f;
                $attr = trim($attr);

                if (!isset($rec[$k])) $rec[$k] = array();
                if (!isset($rec[$k][$word]))
                {
                    $total++;
                    $rec[$k][$word] = array();
                }
                $rec[$k][$word]['tf'] = $tf;
                $rec[$k][$word]['idf'] = $idf;
                $rec[$k][$word]['attr'] = $attr;
            }

            fclose($fd);
        }
        else
        {
            $rec = array();
        }

        // View
        $view = View::factory('dictionary/index')
            ->set('action', URL::base() . 'dictionary/index')
            ->set('current', $current_module)
            ->set('words', $rec)
            ->set('dics', $dic_modules);
        
        $this->response->body($view);
    }

    protected function find_dic($module)
    {
        $path = APPPATH . 'dicts' . DS . $module . DS;

        if (is_file($path . 'dict.utf8.txt'))
        {
            return $path;
        }
        else
        {
            return false;
        }
    }

    public function action_add()
    {
        $word = $this->request->post('word');
        $module = $this->request->post('module');

        $path = $this->find_dic($module);
        $txt = $path . 'dict.utf8.txt';

        $word_info = $word . "\t10.0\t10.0\tn\t\n";

        if (! is_writable($txt))
        {
            $this->ajax->success(false);
            $this->ajax->message('权限不够');
            $this->response->body($this->ajax->build_result());
            return;
        }
            
        $fd = fopen($txt, 'a');
        fwrite($fd, $word_info);

        fclose($fd);

        $this->ajax->data($word . "已写入" . $module . "模块");
        $this->response->body($this->ajax->build_result());
    }

    public function action_write()
    {
        $module = $this->request->post('module');

        $path = $this->find_dic($module);
        $txt = $path . 'dict.utf8.txt';
        $xdb_file = $path . 'dict.utf8.xdb';

        if (is_file($xdb_file))
        {
            unlink($xdb_file);
        }

        if (! is_writable($path) || ! is_writable($path))
        {
            $this->ajax->success(false);
            $this->ajax->message('权限不够');
            $this->response->body($this->ajax->build_result());
            return;
        }

        mb_internal_encoding('UTF-8');

        $fd = fopen($txt, 'r');
        $xdb = new XTreeDB;
        $xdb->Open($xdb_file, 'w');

        $total = 0;
        $rec = array();

        while ($line = fgets($fd, 512))
        {
            if (substr($line, 0, 1) == '#') continue;
            list($word, $tf, $idf, $attr) = explode("\t", $line, 4);
            $k = (ord($word[0]) + ord($word[1])) & 0x3f;
            $attr = trim($attr);

            if (!isset($rec[$k])) $rec[$k] = array();
            if (!isset($rec[$k][$word]))
            {
                $total++;
                $rec[$k][$word] = array();
            }
            $rec[$k][$word]['tf'] = $tf;
            $rec[$k][$word]['idf'] = $idf;
            $rec[$k][$word]['attr'] = $attr;

        
            $len = mb_strlen($word);
            while ($len > 2)
            {
                $len--;
                $temp = mb_substr($word, 0, $len);
                if (!isset($rec[$k][$temp]))
                {
                    $total++;
                    $rec[$k][$temp] = array();
                }
                $rec[$k][$temp]['part'] = 1;
            }
        }

        fclose($fd);

        for ($k = 0; $k < 0x40; $k++)
        {
            if (!isset($rec[$k])) continue;
            $cnt = 0;
            foreach ($rec[$k] as $w => $v)
            {
                $flag = (isset($v['tf']) ? 0x01 : 0);
                if (isset($v['part'])) $flag |= 0x02;
                $data = @pack('ffCa3', $v['tf'], $v['idf'], $flag, $v['attr']);
                $xdb->Put($w, $data);
                $cnt++;
            }
        }
        $xdb->Optimize();
        $xdb->Close();

        $this->ajax->data("已生成" . $module . "模块新的字典");
        $this->response->body($this->ajax->build_result());
    }
}
