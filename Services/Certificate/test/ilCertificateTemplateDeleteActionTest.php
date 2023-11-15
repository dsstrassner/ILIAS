<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

declare(strict_types=1);

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateTemplateDeleteActionTest extends ilCertificateBaseTestCase
{
    public function testDeleteTemplateAndUseOldThumbnail(): void
    {
        $templateRepositoryMock = $this->getMockBuilder(ilCertificateTemplateRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $templateRepositoryMock
            ->method('deleteTemplate')
            ->with(100, 2000);

        $templateRepositoryMock->method('activatePreviousCertificate')
            ->with(2000)
            ->willReturn(new ilCertificateTemplate(
                2000,
                'crs',
                'something',
                md5('something'),
                '[]',
                1,
                'v5.4.0',
                1_234_567_890,
                true,
                'samples/background.jpg'
            ));

        $templateRepositoryMock->expects($this->once())->method("deleteTemplate");
        $templateRepositoryMock->expects($this->once())->method("save");

        $utilHelper = $this->getMockBuilder(ilCertificateUtilHelper::class)
            ->getMock();

        $objectHelper = $this->getMockBuilder(ilCertificateObjectHelper::class)
            ->getMock();

        $objectHelper->method('lookUpType')
            ->willReturn('crs');

        $action = new ilCertificateTemplateDeleteAction(
            $templateRepositoryMock,
            __DIR__,
            'v5.4.0',
            $utilHelper,
            $objectHelper
        );

        $action->delete(100, 2000);
    }

    public function testDeleteTemplateButNoThumbnailWillBeCopiedFromOldCertificate(): void
    {
        $this->expectNotToPerformAssertions();
        $templateRepositoryMock = $this->getMockBuilder(ilCertificateTemplateRepository::class)->getMock();

        $templateRepositoryMock
            ->method('deleteTemplate')
            ->with(100, 2000);

        $templateRepositoryMock->method('activatePreviousCertificate')
            ->with(2000)
            ->willReturn(new ilCertificateTemplate(
                2000,
                'crs',
                'something',
                md5('something'),
                '[]',
                1,
                'v5.4.0',
                1_234_567_890,
                true
            ));

        $utilHelper = $this->getMockBuilder(ilCertificateUtilHelper::class)
            ->getMock();

        $objectHelper = $this->getMockBuilder(ilCertificateObjectHelper::class)
            ->getMock();

        $objectHelper->method('lookUpType')
            ->willReturn('crs');

        $action = new ilCertificateTemplateDeleteAction(
            $templateRepositoryMock,
            __DIR__,
            'v5.4.0',
            $utilHelper,
            $objectHelper
        );

        $action->delete(100, 2000);
    }
}
