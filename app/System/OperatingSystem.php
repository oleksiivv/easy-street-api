<?php

namespace App\System;

class OperatingSystem
{
    public const IOS = 'ios_file_url';

    public const MAC = 'mac_file_url';

    public const WINDOWS = 'windows_file_url';

    public const OTHER = 'linux_file_url';

    public const ANDROID = 'android_file_url';

    public const EXECUTABLE_FILES_PATH = 'upload/app/%s/executables';

    public const PAGE_FILES_PATH = 'upload/app/%s/data/%s';

    public const EXTENSIONS = [
        'android' => 'apk',
    ];

    public const PAGE_FILE_PREFIX_FOR_ICON = 'icon';

    public const PAGE_FILE_PREFIX_FOR_BACKGROUND = 'bg';

    public const PAGE_FILE_PREFIX_FOR_DESCRIPTION = 'desc';

}
