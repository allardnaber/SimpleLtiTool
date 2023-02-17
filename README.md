# SimpleLtiTool

This tool provides an easy method to verify an LTI request and retrieve data from that request for use in your own application.

It is currently based on LTI 1.1. This version is officially deprecated, but will be around for a few years. In the mean time we will try to work on an LTI 1.3 version with the same signature.

## How to use
If you already use Composer, you can easily add the required LTI library to your dependencies. See [composer.json](composer.json) for an example. Then include `SimpleLtiTool` in your own application (be sure to change the namespace). [toolWithComposer.php](toolWithComposer.php) contains an example on how to use the tool.

If you do not use Composer, download the IMSGlobal package from <https://github.com/oat-sa/imsglobal-lti> (Code > Download ZIP) and extract it to the `imsglobal` folder. [toolNoComposer.php](toolNoComposer.php) contains an alternative classloader and an example on how to use the tool.

## Documentation

| Method | Description |
|--------|-------------|
| [**SimpleLtiTool**](#SimpleLtiTool) | This tool provides an easy method to verify an LTI request and retrievedata from that request. |
| [SimpleLtiTool::__construct](#SimpleLtiTool__construct) | Construct the tool launch verifier with specified secrets. |
| [SimpleLtiTool::getCourseCode](#SimpleLtiToolgetCourseCode) | Gets the course code from the LTI launch. It is not included in theContext returned by the IMS library. |
| [SimpleLtiTool::getContext](#SimpleLtiToolgetContext) | Gets the LTI context (= course) from the LTI request. |
| [SimpleLtiTool::getResourceLink](#SimpleLtiToolgetResourceLink) | Gets the Resource Links from the LTI request. The Resource Link is thelink the user clicked to initiate the LTI message. This might be usefulto link items within the tool to specific items in the LMS. |
| [SimpleLtiTool::getUser](#SimpleLtiToolgetUser) | Gets the LTI user from the LTI request. The user id will be anonymized,use getUserName to get the actual username from the LMS. |
| [SimpleLtiTool::getUsername](#SimpleLtiToolgetUsername) | Gets the username for the authenticated user. By default, LTI does notcontain this information. We try the most common fields for LMSs. |
| [SimpleLtiTool::getLocale](#SimpleLtiToolgetLocale) | Gets the current locale for the user in http://www.rfc-editor.org/rfc/bcp/bcp47.txt format. |

## SimpleLtiTool

This tool provides an easy method to verify an LTI request and retrieve
data from that request.

It is currently based on LTI 1.1, which is officially deprecated, but will be
around for a few years. In the meantime we will try to work on an LTI 1.3
version with the same signature.

* Full name: \SimpleLti\SimpleLtiTool


### SimpleLtiTool::__construct

Construct the tool launch verifier with specified secrets.

```php
SimpleLtiTool::__construct( string key, string secret ): mixed
```




**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `key` | **string** | LTI key as configured in the LMS. |
| `secret` | **string** | LTI secret as configured in the LMS. |


**Return Value:**





---
### SimpleLtiTool::getCourseCode

Gets the course code from the LTI launch. It is not included in the
Context returned by the IMS library.

```php
SimpleLtiTool::getCourseCode(  ): string|null
```





**Return Value:**

The Course Code if present, null otherwise.



---
### SimpleLtiTool::getContext

Gets the LTI context (= course) from the LTI request.

```php
SimpleLtiTool::getContext(  ): \IMSGlobal\LTI\ToolProvider\Context
```





**Return Value:**

The Context.



---
### SimpleLtiTool::getResourceLink

Gets the Resource Links from the LTI request. The Resource Link is the
link the user clicked to initiate the LTI message. This might be useful
to link items within the tool to specific items in the LMS.

```php
SimpleLtiTool::getResourceLink(  ): \IMSGlobal\LTI\ToolProvider\ResourceLink
```





**Return Value:**

The LTI Resource Link
that initiated this request.



---
### SimpleLtiTool::getUser

Gets the LTI user from the LTI request. The user id will be anonymized,
use getUserName to get the actual username from the LMS.

```php
SimpleLtiTool::getUser(  ): \IMSGlobal\LTI\ToolProvider\User
```





**Return Value:**

The LTI User that performed this request.


**See Also:**

* \SimpleLti\SimpleLtiTool::getUserName() - The method to get the actual username.

---
### SimpleLtiTool::getUsername

Gets the username for the authenticated user. By default, LTI does not
contain this information. We try the most common fields for LMSs.

```php
SimpleLtiTool::getUsername(  ): string|null
```

Source: https://developers.exlibrisgroup.com/leganto/integrations/lti/troubleshooting/user-problems/lms-username-parameter/



**Return Value:**

The Brightspace username if defined, null otherwise.



---
### SimpleLtiTool::getLocale

Gets the current locale for the user in http://www.rfc-editor.org/rfc/bcp/bcp47.txt format.

```php
SimpleLtiTool::getLocale(  ): string|null
```





**Return Value:**

The locale identifier if present, null otherwise.



---
