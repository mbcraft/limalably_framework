<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

interface LITagRenderingTips {

    const TAG_MODE_AUTO = 0;
    const TAG_MODE_OPEN_CONTENT_CLOSE = 1;
    const TAG_MODE_OPEN_EMPTY_CLOSE = 2;
    const TAG_MODE_OPEN_ONLY = 3;
    const TAG_MODE_OPENCLOSE_NO_CONTENT = 4;

    const TAG_INDENT_AUTO = 0;
    const TAG_INDENT_SKIP_ALL = 1;
    const TAG_INDENT_NORMAL = 2;
    
}