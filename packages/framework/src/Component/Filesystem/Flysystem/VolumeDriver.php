<?php

namespace Shopsys\FrameworkBundle\Component\Filesystem\Flysystem;

use Barryvdh\elFinderFlysystemDriver\Driver;
use elFinder;
use Shopsys\FrameworkBundle\Component\Filesystem\Flysystem\Plugin\GetUrl;

class VolumeDriver extends Driver
{
    protected function configure()
    {
        @parent::configure();

        $thumbnailPath = $this->options['tmbPath'];

        if ($thumbnailPath) {
            if (!$this->fs->has($thumbnailPath)) {
                if ($this->_mkdir($thumbnailPath, '')) {
                    $this->_chmod($thumbnailPath, $this->options['tmbPathMode']);
                } else {
                    $thumbnailPath = '';
                }
            }

            $stat = $this->_stat($thumbnailPath);

            if ($this->_dirExists($thumbnailPath) && $stat['read']) {
                $this->tmbPath = $thumbnailPath;
                $this->tmbPathWritable = $stat['write'];
            }
        }

        $this->fs->addPlugin(new GetUrl($this->options));
    }

    /**
     * @param string $hash
     * @return false|string
     */
    public function tmb($hash)
    {
        $thumbnailPath = $this->decode($hash);
        $stat = $this->_stat($thumbnailPath, $hash);

        if (isset($stat['tmb'])) {
            $res = $stat['tmb'] == '1' ? $this->createTmb($thumbnailPath, $stat) : $stat['tmb'];

            if (!$res) {
                list($type) = explode('/', $stat['mime']);
                $fallback = $this->options['resourcePath'] . DIRECTORY_SEPARATOR . strtolower($type) . '.png';
                if (is_file($fallback)) {
                    $res = $this->tmbname($stat);
                    if (!$this->fs->put($fallback, $this->createThumbnailPath($res))) {
                        $res = false;
                    }
                }
            }
            return $res;
        }
        return false;
    }

    /**
     * @param string $thumbnailPath
     * @param mixed[] $stat
     * @return false|string
     */
    protected function gettmb($thumbnailPath, $stat)
    {
        if ($this->tmbURL && $this->tmbPath) {
            // file itself thumnbnail
            if (strpos($thumbnailPath, $this->tmbPath) === 0) {
                return basename($thumbnailPath);
            }

            $stat['hash'] = $stat['hash'] ?? '';
            $name = $this->tmbname($stat);
            if ($this->fs->has($this->createThumbnailPath($name))) {
                return $name;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return string
     */
    public function createThumbnailPath($name)
    {
        return $this->tmbPath . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * @param string $thumbnailPath
     * @param mixed[] $stat
     * @return false|string
     */
    protected function createTmb($thumbnailPath, $stat)
    {
        $tmpThumbnailPath = $this->tmbPath;
        $this->tmbPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $tmpThumbnailPath;
        @mkdir($this->tmbPath, 0777, true);

        $name = parent::createTmb($thumbnailPath, $stat);
        if ($name !== false) {
            $fp = fopen($this->createThumbnailPath($name), 'rb');
            if ($fp === false) {
                return false;
            }
            $this->_save($fp, $tmpThumbnailPath, $name, $stat);
            unlink($this->createThumbnailPath($name));
        }

        return $name;
    }

    /**
     * @param mixed[] $stat
     */
    protected function rmTmb($stat)
    {
        $path = $stat['realpath'];
        if ($this->tmbURL) {
            $thumbnailName = $this->gettmb($path, $stat);
            $stat['tmb'] = $thumbnailName ? $thumbnailName : 1;
        }

        if ($this->tmbPathWritable) {
            if ($stat['mime'] === 'directory') {
                foreach ($this->scandirCE($this->decode($stat['hash'])) as $p) {
                    elFinder::extendTimeLimit(30);
                    $name = $this->basenameCE($p);
                    $name != '.' && $name != '..' && $this->rmTmb($this->stat($p));
                }
            } elseif (!empty($stat['tmb']) && $stat['tmb'] != '1') {
                $thumbnailPath = $this->createThumbnailPath(rawurldecode($stat['tmb']));
                $this->_unlink($thumbnailPath);
                clearstatcache();
            }
        }
    }

    /**
     * @param string $path
     * @param string $hash
     * @return false|mixed[]
     */
    protected function _stat($path, $hash = '')
    {
        $stat = parent::_stat($path);
        if ($hash !== '') {
            $stat['hash'] = $hash;
        }

        if ($this->tmbURL && !isset($stat['tmb']) && $this->canCreateTmb($path, $stat)) {
            $thumbnailName = $this->gettmb($path, $stat);
            $stat['tmb'] = $thumbnailName ? $thumbnailName : 1;
        }

        return $stat;
    }
}

class_alias(VolumeDriver::class, 'elFinderVolumeFlysystem');
