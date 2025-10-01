<?php

namespace Services\Export;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Writer\WriterInterface;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Course;

final class CourseMemberService
{
    public const WORD  = 'docx';
    public const EXCEL = 'xlsx';
    public const CSV   = 'csv';

    protected string $filepath;
    protected string $export_format = CourseMemberService::WORD;

    public function __construct(
        protected Course $course,
        protected string $status = ''
    )
    {
        $this->filepath = tempnam($GLOBALS['TMP_PATH'], 'documents');
    }

    /**
     * Generates a Word document containing the course member list.
     *
     * @return WriterInterface
     * @throws Exception
     */
    public function getWordFile(): WriterInterface
    {
        $members = $this->extractMembers();

        $word    = new PhpWord();
        $section = $word->addSection();

        $footer = $section->addFooter();
        $footer->addText(
            sprintf(_('Erstellt am: %s'), date('d.m.Y H:i')),
            ['italic' => true, 'size' => 8],
            ['alignment' => Jc::END]
        );
        $properties = $word->getSettings();
        $properties->setThemeFontLang(new Language($GLOBALS['_language']));

        $section->addText(
            $this->getHeadline(),
            ['bold' => true, 'size' => 14]
        );
        $section->addTextBreak();

        $section->addText(
            $this->getListType(),
            ['bold' => true, 'size' => 12],
        );

        $table_style = [
            'borderSize'       => 4,
            'borderColor'      => '000000',
            'cellMarginTop'    => 0,
            'cellMarginBottom' => 0,
            'cellMarginLeft'   => 40,
            'cellMarginRight'  => 40,
        ];

        $cell_style = [
            'spaceBefore' => 0,
            'spaceAfter'  => 0
        ];

        $text_style    = ['size' => 8];
        $firstRowStyle = ['tblHeader' => true];

        $word->addTableStyle('MembersTable', $table_style, $firstRowStyle);

        $word->addNumberingStyle('degree_bullets', [
            'type'   => 'multilevel',
            'levels' => [
                [
                    'format'  => 'bullet',
                    'text'    => '•',
                    'left'    => 200,
                    'hanging' => 200,
                    'tabPos'  => 200,
                ]
            ],
        ]);

        $table = $section->addTable('MembersTable');

        $headers = [
            _('Name'),
            _('E-Mail'),
            _('Telefon'),
            _('Studiengänge')
        ];

        $table->addRow();
        foreach ($headers as $h) {
            $table->addCell(
                2500,
                [
                    'valign'  => 'center',
                    'bgColor' => 'BFBFBF'
                ]
            )->addText(
                $h,
                [
                    'bold' => true,
                    'size' => 10,
                ],
                $cell_style
            );
        }

        foreach ($members as $status => $users) {
            $table->addRow();
            $table->addCell(
                14000,
                [
                    'gridSpan' => 4,
                    'bgColor'  => 'D9D9D9'
                ]
            )->addText(
                get_title_for_status($status, count($users)),
                [
                    'bold' => true,
                    'size' => 10
                ],
                $cell_style
            );

            if (!empty($users)) {
                foreach ($users as $user) {
                    $table->addRow();
                    $table->addCell(3000, ['noWrap' => true])
                        ->addText(
                            htmlReady($user['Nachname'] . ', ' . $user['Vorname']),
                            $text_style,
                            $cell_style
                        );
                    $table->addCell(4500, ['noWrap' => true])
                        ->addText(
                            htmlReady($user['Email']),
                            $text_style,
                            $cell_style
                        );
                    $table->addCell(2000)
                        ->addText(
                            htmlReady($user['privatnr']),
                            $text_style,
                            $cell_style
                        );

                    $cell = $table->addCell(2400);
                    if (!empty($user['studiengaenge'])) {
                        $degrees = explode(';', $user['studiengaenge']);
                        if (count($degrees) > 1) {
                            foreach ($degrees as $degree) {
                                $cell->addListItem(
                                    trim($degree),
                                    0,
                                    $text_style,
                                    'degree_bullets',
                                    [
                                        'spaceBefore' => 0,
                                        'spaceAfter'  => 60,
                                    ]
                                );
                            }
                        } else {
                            $cell->addText($user['studiengaenge'], $text_style, $cell_style);
                        }
                    } else {
                        $cell->addText('', $text_style, $cell_style);
                    }
                }
            }
        }

        return IOFactory::createWriter($word);
    }

    /**
     * Extracts and organizes course member data by status.
     *
     * @return array
     */
    public function extractMembers(): array
    {
        $members  = $this->course->getMembersData($this->status);
        $_members = [];
        foreach ($members as $user_id => $data) {
            $_members[$data['status']][$user_id] = $data;
        };
        return $_members;
    }

    /**
     * Saves the generated Word document to the configured file path.
     *
     * @return void
     * @throws Exception
     */

    public function save(): void
    {
        if ($this->export_format === CourseMemberService::WORD) {
            $this->getWordFile()
                ->save($this->filepath);
        }
    }

    /**
     * @param string $filepath
     * @return $this
     */
    public function setFilePath(string $filepath): self
    {
        $this->filepath = $filepath;
        return $this;
    }

    /**
     * Returns the full path where the Word document will be saved.
     *
     * @return string The absolute file path for the generated document.
     */
    public function getFilePath(): string
    {
        return $this->filepath;
    }

    /**
     * Generates the headline for the Word document.
     *
     * @return string
     */
    private function getHeadline(): string
    {
        return sprintf('%s: %s', $this->getListType(), $this->course->getFullName());
    }

    /**
     * Returns the type of list based on the current status.
     *
     * @return string
     */
    public function getListType(): string
    {
        if (in_array($this->status, ['awaiting', 'claiming'])) {
            return _('Warteliste');
        }
        return _('Teilnehmendenliste');
    }

    /**
     * Generates the filename for the exported Word document.
     *
     * @return string
     */
    public function getFilename(): string
    {
        $file_name = _('Teilnehmendenexport');
        if (in_array($this->status, ['awaiting', 'claiming'])) {
            $file_name = _('Wartelistenexport');
        }
        return $file_name . '.' . $this->export_format;
    }


    /**
     * @return string
     */
    public function getExportFormat(): string
    {
        return $this->export_format;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setExportFormat(string $format): self
    {
        $this->export_format = $format;

        return $this;
    }

}
