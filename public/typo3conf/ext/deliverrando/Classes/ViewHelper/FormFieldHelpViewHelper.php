<?php

namespace MyVendor\Deliverrando\ViewHelper;

//NOTE: The -TagBased- is required so that HTML does not get escaped!
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class FormFieldHelpViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments() : void
    {
        parent::initializeArguments();

        $this->registerArgument("tag", "string", "The tag to add to the form", true);
        $this->registerArgument("idPrefix", "string", "The string to add to the id of the tag and the p tag", true);
        $this->registerArgument("placeholder", "string", "An optional placeholder", false);
    }

    /**
     * @return string
     */
    public function render() : string
    {
        $result = $this->arguments['tag'];
        $endTag = '/>';

        assert(strncmp($result, '<', 1) == 0);

        $tagEnd = '/>';
        $endTagPos = strrpos($result, $tagEnd);
        if($endTagPos !== false) {
            $fullTagSize = strlen($result);
            $tagEndSize = strlen($tagEnd);
            assert($endTagPos == (strlen($result) - strlen($tagEnd)));
            $result = substr($result, 0, $endTagPos);
        } else {
            $firstSpace = strpos($result, ' ');
            $tagName = substr($result, 1, $firstSpace - 1);
            $endTag = '></' . $tagName . '>';
            $closeingOpenTag = strpos($result, '>');
            //NOTE: This does not work because of htmlspecialchars-encoding stuff
            //assert(strcmp(substr($result, $closeingOpenTag, 3), '></') == 0);
            $result = substr($result, 0, $closeingOpenTag);
        }

        $nameAttributeText = 'name="';
        $nameArgumentStartPos = strpos($result, $nameAttributeText);
        $nameArgumentStartPos += strlen($nameAttributeText);
        assert($nameArgumentStartPos < strlen($result));

        $classNameStartPos = strpos($result, '[', $nameArgumentStartPos) + 1;
        assert($classNameStartPos < strlen($result));
        $classNameEndPos = strpos($result, ']', $classNameStartPos);
        assert($classNameEndPos < strlen($result));
        $className = substr($result, $classNameStartPos, $classNameEndPos - $classNameStartPos);

        $propertyNameStartPos = strpos($result, '[', $classNameEndPos) + 1;
        assert($propertyNameStartPos < strlen($result));
        $propertyNameEndPos = strpos($result, ']', $propertyNameStartPos);
        assert($propertyNameEndPos < strlen($result));
        $propertyName = substr($result, $propertyNameStartPos, $propertyNameEndPos - $propertyNameStartPos);

        if(isset($this->arguments['placeholder'])) {
            $placeholderName = $this->arguments['placeholder'];
        } else {
            $placeholderName = ucfirst($propertyName);
        }

        $idPrefix = 'formErr_' . $this->arguments['idPrefix'] . '_';
        $id = $idPrefix . $className . '.' . $propertyName;

        $result = sprintf('%s placeholder="%s" id="%s" %s <p id="msg_%s"></p> <br />', $result, $placeholderName, $id, $endTag, $id);

        return $result;
    }
}