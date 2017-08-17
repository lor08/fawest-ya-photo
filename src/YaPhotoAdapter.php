<?php

namespace Lor08\YaPhoto;

use Lor08\YaPhoto\YaPhotoClient;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;

class YaPhotoAdapter extends AbstractAdapter
{
	protected $client;

	public function __construct(YaPhotoClient $client)
	{
		$this->client = $client;
	}

	public function write($path, $contents, Config $config)
	{
		return $this->upload($path, $contents, 'add');
	}

	public function writeStream($path, $resource, Config $config)
	{
		return $this->upload($path, $resource, 'add');
	}

	public function update($path, $contents, Config $config)
	{
		return $this->upload($path, $contents, 'overwrite');
	}

	public function updateStream($path, $resource, Config $config)
	{
		return $this->upload($path, $resource, 'overwrite');
	}

	public function rename($path, $newpath)
	{
		// TODO: Implement rename() method.
	}

	public function copy($path, $newpath)
	{
		// TODO: Implement copy() method.
	}

	public function delete($path)
	{
		// TODO: Implement delete() method.
	}

	public function deleteDir($dirname)
	{
		// TODO: Implement deleteDir() method.
	}

	public function createDir($dirname, Config $config)
	{
		$apiUrl = $this->client->getServiceDocument()->getUrlAlbumsCollection() . '?format=json';
		$transport = $this->client->getTransport();
		$atomEntry = "<entry xmlns=\"http://www.w3.org/2005/Atom\" xmlns:f=\"yandex:fotki\"><title>$dirname</title></entry>";
		$tmp = $transport->post($apiUrl, ['atomEntry' => $atomEntry]);
		dd($tmp);
	}

	public function setVisibility($path, $visibility)
	{
		// TODO: Implement setVisibility() method.
	}

	public function has($path)
	{
		// TODO: Implement has() method.
	}

	public function read($path)
	{
		// TODO: Implement read() method.
	}

	public function readStream($path)
	{
		// TODO: Implement readStream() method.
	}

	public function listContents($directory = '', $recursive = false) : array
	{
		$response['path_display'] = public_path('upload');
		$response['server_modified'] = date('Y-m-d');
		$response['size'] = 232323;
		$response['.tag'] = 'folder';
		$result[] = $this->normalizeResponse($response);
		dd($result);
		return $result;
	}

	public function getMetadata($path)
	{
		// TODO: Implement getMetadata() method.
	}

	public function getSize($path)
	{
		// TODO: Implement getSize() method.
	}

	public function getMimetype($path)
	{
		// TODO: Implement getMimetype() method.
	}

	public function getTimestamp($path)
	{
		// TODO: Implement getTimestamp() method.
	}

	public function getVisibility($path)
	{
		// TODO: Implement getVisibility() method.
	}

	protected function upload(string $path, $contents, string $mode)
	{
		try {
			$object = $this->client->sendPhoto(array('image'=> $contents), $path);
		} catch (\Exception $e) {
			return false;
		}
		return $this->normalizeResponse($object);
	}

	protected function normalizeResponse(array $response): array
	{
//		$normalizedPath = $response['img']['orig']['href'];
//		$normalizedResponse = ['path' => $normalizedPath];
//		if (isset($response['server_modified'])) {
//			$normalizedResponse['timestamp'] = strtotime($response['server_modified']);
//		}
//		if (isset($response['size'])) {
//			$normalizedResponse['size'] = $response['size'];
//			$normalizedResponse['bytes'] = $response['size'];
//		}
//		$type = ($response['.tag'] === 'folder' ? 'dir' : 'file');
//		$normalizedResponse['type'] = $type;
		$normalizedResponse = [
			'timestamp' => strtotime($response['published']),
			'path' => $response['img']['orig']['href'],
			'size' => $response['img']['orig']['bytesize'],
			'type' => 'file',
		];
//		dd($normalizedResponse);
		return $normalizedResponse;
	}
}