<?hh // decl

function empty(mixed $x): bool;

class DOMNode {
    public string $nodeName;
    public string $nodeValue;
    public  int $nodeType;
    public  DOMNode $parentNode;
    public  DOMNodeList $childNodes;
    public  DOMNode $firstChild;
    public  DOMNode $lastChild;
    public  DOMNode $previousSibling;
    public  DOMNode $nextSibling;
    public  DOMNamedNodeMap $attributes;
    public  DOMDocument $ownerDocument;
    public  string $namespaceURI;
    public  string $prefix;
    public  string $localName;
    public  string $baseURI;
    public  string $textContent ;
    public function appendChild (DOMNode $newnode): DOMNode {}
    public function C14N (?bool $exclusive, ?bool $with_comments, ?array $xpath, ?array $ns_prefixes): string {}
    public function C14NFile (string $uri, ?bool $exclusive, ?bool $with_comments, ?array $xpath, ?array $ns_prefixes): int {}
    public function cloneNode (?bool $deep): DOMNode {}
    public function getLineNo (): int {}
    public function getNodePath (): string {}
    public function hasAttributes (): bool {}
    public function hasChildNodes (): bool {}
    public function isDefaultNamespace ( string $namespaceURI ): bool {}
    public function isSameNode ( DOMNode $node ): bool {}
    public function isSupported ( string $feature , string $version ): bool {}
    public function lookupNamespaceURI ( string $prefix ): string {}
    public function lookupPrefix ( string $namespaceURI ): string {}
    public function normalize (): void {}
    public function removeChild ( DOMNode $oldnode ): DOMNode {}
    public function replaceChild ( DOMNode $newnode , DOMNode $oldnode ): DOMNode {}
}

class DOMText extends DOMNode {

    public string $wholeText;
    public function __construct (?string $value) {}
    public function isWhitespaceInElementContent(): bool {}
    public function splitText ( int $offset ): DOMText {}
}

class DOMElement extends DOMNode {
    public bool $schemaTypeInfo ;
    public string $tagName ;

    public function __construct (string $name, ?string $value, ?string $namespaceURI) {}
    public function getAttribute ( string $name ): string {}
    public function getAttributeNode ( string $name ): DOMAttr{}
    public function getAttributeNodeNS ( string $namespaceURI , string $localName ): DOMAttr {}
    public function getAttributeNS ( string $namespaceURI , string $localName ): string {}
    public function getElementsByTagName ( string $name ): DOMNodeList {}
    public function getElementsByTagNameNS ( string $namespaceURI , string $localName ): DOMNodeList {}
    public function hasAttribute ( string $name ): bool {}
    public function hasAttributeNS ( string $namespaceURI , string $localName ): bool {}
    public function removeAttribute ( string $name ): bool {}
    public function removeAttributeNode ( DOMAttr $oldnode ): bool {}
    public function removeAttributeNS ( string $namespaceURI , string $localName ): bool {}
    public function setAttribute ( string $name , string $value ): DOMAttr {}
    public function setAttributeNode ( DOMAttr $attr ): DOMAttr {}
    public function setAttributeNodeNS ( DOMAttr $attr ): DOMAttr {}
    public function setAttributeNS ( string $namespaceURI , string $qualifiedName , string $value ): void {}
    public function setIdAttribute ( string $name , bool $isId ): void {}
    public function setIdAttributeNode ( DOMAttr $attr , bool $isId ): void {}
    public function setIdAttributeNS ( string $namespaceURI , string $localName , bool $isId ): void {}
}

class DOMDocument extends DOMNode {
    public string $actualEncoding;
    public DOMConfiguration $config ;
    public DOMDocumentType $doctype ;
    public DOMElement $documentElement ;
    public string $documentURI ;
    public string $encoding ;
    public bool $formatOutput ;
    public DOMImplementation $implementation ;
    public bool $preserveWhiteSpace = true ;
    public bool $recover ;
    public bool $resolveExternals ;
    public bool $standalone ;
    public bool $strictErrorChecking = true ;
    public bool $substituteEntities ;
    public bool $validateOnParse = false ;
    public string $version ;
    public string $xmlEncoding ;
    public bool $xmlStandalone ;
    public string $xmlVersion ;
    public function __construct (?string $version = null, ?string $encoding = null) {}
    public function createAttribute ( string $name ): DOMAttr {}
    public function createAttributeNS ( string $namespaceURI , string $qualifiedName ): DOMAttr {}
    public function createCDATASection ( string $data ): DOMCDATASection {}
    public function createComment ( string $data ): DOMComment {}
    public function createDocumentFragment (  ): DOMDocumentFragment {}
    public function createElement ( string $name, ?string $value = null): DOMElement {}
    public function createElementNS ( string $namespaceURI , string $qualifiedName, ?string $value): DOMElement {}
    public function createEntityReference ( string $name ): DOMEntityReference {}
    public function createProcessingInstruction ( string $target, ?string $data): DOMProcessingInstruction {}
    public function createTextNode ( string $content ): DOMText {}
    public function getElementById ( string $elementId ): DOMElement {}
    public function getElementsByTagNameNS ( string $namespaceURI , string $localName ): DOMNodeList {}
    public function importNode ( DOMNode $importedNode, ?bool $deep): DOMNode {}
    public function load ( $filename, ?int $options = 0): mixed {}
    public function loadHTML ( string $source, ?int $options = 0): bool {}
    public function loadHTMLFile ( string $filename, ?int $options = 0 ): bool {}
    public function loadXML ( string $source, ?int $options = 0 ): mixed {}
    public function  normalizeDocument (  ): void {}
    public function registerNodeClass ( string $baseclass , string $extendedclass ): bool {}
    public function relaxNGValidate ( string $filename ): bool {}
    public function relaxNGValidateSource ( string $source ): bool {}
    public function save ( string $filename, ?int $options): int {}
    public function saveHTML (?DOMNode $node = NULL): string {}
    public function saveHTMLFile ( string $filename ): int {}
    public function saveXML (?DOMNode $node, ?int $options): string {}
    public function schemaValidate ( string $filename, ?int $flags): bool {}
    public function schemaValidateSource ( string $source, ?int $flags): bool {}
    public function validate (): bool {}
    public function xinclude (?int $options): int {}
}