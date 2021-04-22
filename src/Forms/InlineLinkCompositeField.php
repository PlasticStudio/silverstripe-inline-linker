<?php

namespace NSWDPC\InlineLinker;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;

/**
 * Subclass for specific composite field handling, currently not in use
 * Could be useful for future configuration
 */
class InlineLinkCompositeField extends CompositeField
{

    public function __construct($name, $title, $parent) {

        $children = FieldList::create();

        $inline_link_field = InlineLinkField::create($name, $title, $parent);

        // determine if in the context of an inline editable Elemental element
        $inline_editable = $inline_link_field->hasInlineElementalParent();

        $current = $inline_link_field->CurrentLink();

        /**
         * If there is a current link,
         * render a header field and the template for the current link
         * A link might exist without a
         */
        if($current && $current->Type) {
            $children->push(
                HeaderField::create(
                    $name . "_CurrentLinkHeader",
                    _t("NSWDPC\\InlineLinker\\InlineLinkField.CURRENT_LINK_HEADER", "Current link")
                )
            );
            $children->push(
                // @var LiteralField
                $current
            );
        }

        $link_title_field = TextField::create(
            $inline_link_field->prefixedFieldName( InlineLinkField::FIELD_NAME_TITLE ),
            _t("NSWDPC\\InlineLinker\\InlineLinkField.LINK_TITLE", 'Title'),
            $inline_link_field->getRecordTitle()
        );

        $link_openinnewwindow_field = CheckboxField::create(
            $inline_link_field->prefixedFieldName( InlineLinkField::FIELD_NAME_OPEN_IN_NEW_WINDOW),
            _t("NSWDPC\\InlineLinker\\InlineLinkField.LINK_OPEN_IN_NEW_WINDOW", 'Open in new window'),
            $inline_link_field->getRecordOpenInNewWindow()
        );

        // to save these fields, the InlineLinkField needs to know about them
        $inline_link_field->setTitleField( $link_title_field );
        $inline_link_field->setOpenInNewWindowField( $link_openinnewwindow_field );

        $children->push(
            $link_title_field
        );

        $children->push(
            $link_openinnewwindow_field
        );

        $children->push(
            $inline_link_field
        );

        // push all child fields
        parent::__construct($children);

        // set name and title AFTER the parent composite is created
        $this->setName($name . "_InlinkLinkComposite");

        // handle inline editable element by using a fieldset/legend
        if($inline_editable) {
            $this->setLegend($title);
            $this->setTag('fieldset');
        } else {
            $this->setTitle($title);
            $this->setTag('div');
        }


    }

}
