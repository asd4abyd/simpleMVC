<?php

namespace Core\Libraries;

class Config
{
    protected $config=[];

    public function __invoke($config)
    {
        $config = trim($config, '.');
        $config = preg_replace('/[\.]+/', '.', $config);
        $param = explode('.', $config, 2);

        $param[0] = strtolower($param[0]);

        if(!isset($this->config[$param[0]])){
            if(!file_exists(RESOURCES_PATH.'config/'.$param[0].'.php')){
                return null;
            }
            $this->loadConfig($param[0]);
        }


        return $this->getConfigValue($param[0], $param[1]);
    }

    private function loadConfig(string $configFile)
    {
       $this->config[$configFile] = include RESOURCES_PATH.'config/'.$configFile.'.php';
    }

    private function getConfigValue(string $configFile, string $configPath)
    {
        $configPathArr = explode('.', $configPath);

        $config = $this->config[$configFile];

        foreach ($configPathArr as $key){
            $config=$config[$key]??null;

            if(is_null($config)){
                break;
            }
        }

        return $config;
    }

}