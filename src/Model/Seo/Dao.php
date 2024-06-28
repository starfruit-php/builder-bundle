<?php

namespace Starfruit\BuilderBundle\Model\Seo;

use Pimcore\Model\Dao\AbstractDao;
use Pimcore\Model\Exception\NotFoundException;
use Starfruit\BuilderBundle\Service\DatabaseService;
use Starfruit\BuilderBundle\Service\RedirectService;

class Dao extends AbstractDao
{
    protected string $tableName = DatabaseService::BUILDER_SEO_TABLE;

    public function getById(?int $id = null): void
    {
        if ($id !== null)  {
            $this->model->setId($id);
        }

        $data = $this->db->fetchAssociative('SELECT * FROM '.$this->tableName.' WHERE id = ?', [$this->model->getId()]);

        if (!$data) {
            throw new NotFoundException("Builder SEO with the ID " . $this->model->getId() . " doesn't exists");
        }

        $this->assignVariablesToModel($data);
    }

    public function getByElement(?int $element, $language): void
    {
        $this->model->setElement($element);
        $this->model->setLanguage($language);

        $data = $this->db->fetchAssociative('SELECT * FROM '.$this->tableName.' WHERE element = ? AND language = ?', [$this->model->getElement(), $this->model->getLanguage()]);

        if (!$data) {
            throw new NotFoundException("Builder SEO with the element " . $this->model->getElement() . " doesn't exists");
        }

        $this->assignVariablesToModel($data);
    }

    public function getByObject(?int $element, $language): void
    {
        $this->model->setElement($element);
        $this->model->setLanguage($language);

        $data = $this->db->fetchAssociative('SELECT * FROM '.$this->tableName.' WHERE element = ? AND language = ?', [$this->model->getElement(), $this->model->getLanguage()]);

        if (!$data) {
            throw new NotFoundException("Builder SEO with the object " . $this->model->getElement() . " doesn't exists");
        }

        $this->assignVariablesToModel($data);
    }

    public function getByDocument(?int $element): void
    {
        $this->model->setElement($element);

        $data = $this->db->fetchAssociative('SELECT * FROM '.$this->tableName.' WHERE element = ?', [$this->model->getElement()]);

        if (!$data) {
            throw new NotFoundException("Builder SEO with the document " . $this->model->getElement() . " doesn't exists");
        }

        $this->assignVariablesToModel($data);
    }

    public function save(): void
    {
        $vars = get_object_vars($this->model);

        $buffer = [];

        $validColumns = $this->getValidTableColumns($this->tableName);

        if (count($vars)) {
            foreach ($vars as $k => $v) {
                if (!in_array($k, $validColumns)) {
                    continue;
                }

                $getter = "get" . ucfirst($k);

                if (!is_callable([$this->model, $getter])) {
                    continue;
                }

                $value = $this->model->$getter();

                if (is_bool($value)) {
                    $value = (int)$value;
                }

                $buffer[$k] = $value;
            }
        }

        // update redirectId
        $redirectId = RedirectService::getId($this->model);
        $buffer['redirectId'] = $redirectId;

        if ($this->model->getId() !== null) {
            $this->db->update($this->tableName, $buffer, ["id" => $this->model->getId()]);
            return;
        }

        $this->db->insert($this->tableName, $buffer);
        $this->model->setId($this->db->lastInsertId());
    }

    public function delete(): void
    {
        $this->db->delete($this->tableName, ["id" => $this->model->getId()]);
    }

}