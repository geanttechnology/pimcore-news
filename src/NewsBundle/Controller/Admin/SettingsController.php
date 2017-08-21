<?php

namespace NewsBundle\Controller\Admin;

use NewsBundle\Manager\EntryTypeManager;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\Object;
use Pimcore\Model\Version;
use Pimcore\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends AdminController
{

    /**
     * @param Request $request
     *
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getEntryTypesAction(Request $request)
    {
        /** @var EntryTypeManager $configuration */
        $entryTypeManager = $this->container->get('news.manager.entry_types');

        /** @var Translator $translator */
        $translator = $this->container->get('pimcore.translator');

        $newsObject = Object::getById(intval($request->get('objectId')));
        $newsTypes = $entryTypeManager->getTypes($newsObject);

        $valueArray = [];
        foreach ($newsTypes as $typeName => $type) {
            $valueArray[] = [
                'custom_layout_id' => $type['custom_layout_id'],
                'value'            => $typeName,
                'key'              => $translator->trans($type['name'], [], 'admin'),
                'default'          => $entryTypeManager->getDefaultType()
            ];
        }

        return $this->json([
            'options' => $valueArray,
            'success' => TRUE,
            'message' => ''
        ]);
    }

    public function changeEntryTypeAction(Request $request)
    {
        $entryTypeId = $request->get('entryTypeId');
        $object = Object::getById(intval($request->get('objectId')));

        if ($object instanceof Object\NewsEntry) {
            $object->setEntryType($entryTypeId);
            Version::disable();
            $object->save();
            Version::enable();
        }

        return $this->json([
            'entryTypeId' => $entryTypeId,
            'success'     => TRUE,
            'message'     => ''
        ]);
    }

}