<?php
class ControllerExtensionModuleOptimizingCircularLink extends Controller
{
    public function change_categories(&$data)
    {
        $url = $this->get_this_url();
        $this->take_apart($data, $url);
        return $data;
    }

    public function change_breadcrumbs($route, &$data, $output)
    {
        $url = $this->get_this_url();
        if (is_array($data)) {
            foreach ($data as $key => &$info) {
                if ($key === 'breadcrumbs') {
                    $this->take_apart($info, $url);
                } else {
                    $this->change_breadcrumbs('', $info, '');
                }
            }
        }
    }

    public function get_this_url()
    {
        $get = $this->request->get;
        if (isset($get['route'])) {
            $route = $get['route'];
            unset ($get['route']);
        } else {
            $route = '';
        }

        foreach ($get as $i => $get_param) {
            $get[$i] = $i . '=' . $get_param;
        }

        $url = $this->url->link($route, implode('&', $get));

        return $url;
    }

    public function take_apart(&$array, $disable_url) {
        foreach ($array as $x => $i) {
            if (is_array($i)) {
                $array[$x] = $this->take_apart($i, $disable_url);
            } else {
                if ($i === $disable_url) {
                    $array[$x] = 'javascript:location=location';
                }
            }
        }
        return $array;
    }

}