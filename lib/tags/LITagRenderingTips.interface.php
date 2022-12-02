<?php

interface LITagRenderingTips {

    const TAG_MODE_AUTO = 0;
    const TAG_MODE_OPEN_CONTENT_CLOSE = 1;
    const TAG_MODE_OPEN_EMPTY_CLOSE = 2;
    const TAG_MODE_OPEN_ONLY = 3;
    const TAG_MODE_OPENCLOSE_NO_CONTENT = 4;

    const INDENT_MODE_AUTO = 0;
    const INDENT_MODE_SKIP_ALL = 1;
    const INDENT_MODE_SKIP_START = 2;
    const INDENT_MODE_NORMAL = 3;
    
}