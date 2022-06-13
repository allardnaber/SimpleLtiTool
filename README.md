# SimpleLtiTool

This tool provides an easy method to verify an LTI request and retrieve data from that request for use in your own application.

It is currently based on LTI 1.1. This version is officially deprecated, but will be around for a few years. In the mean time we will try to work on an LTI 1.3 version with the same signature.

## How to use
If you already use Composer, you can easily add the required LTI library to your dependencies. See [composer.json](composer.json) for an example. Then include `SimpleLtiTool` in your own application (be sure to change the namespace). [toolWithComposer.php](toolWithComposer.php) contains an example on how to use the tool.

If you do not use Composer, download the IMSGlobal package from <https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP> (Code > Download ZIP) and extract it to the `imsglobal` folder. [toolNoComposer.php](toolNoComposer.php) contains an alternative classloader and an example on how to use the tool.

## Please note
In case the tool exits with error **Array and string offset access syntax with curly braces is deprecated**, edit `imsglobal/lti/src/OAuth/OAuthSignatureMethod.php`, line 59. Replace the curly braces `{ }` with brackets `[ ]`.