<?php

namespace ShopSys\CodingStandards\CsFixer;

use SplFileInfo;
use Symfony\CS\AbstractFixer;
use Symfony\CS\DocBlock\Annotation;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;

class OrmJoinColumnRequireNullableFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
            $doc = new DocBlock($token->getContent());
            foreach ($doc->getAnnotations() as $annotation) {
                if ($this->isRelationAnnotation($annotation)) {
                    $this->fixRelationAnnotation($doc, $annotation);
                    $token->setContent($doc->getContent());
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Annotations @ORM\ManyToOne and @ORM\OneToOne must have defined nullable option in @ORM\JoinColumn';
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::NONE_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(SplFileInfo $file)
    {
        return preg_match('/\.php(?:\.twig)?$/ui', $file->getFilename()) === 1;
    }

    /**
     * @param \Symfony\CS\DocBlock\Annotation $annotation
     *
     * @return bool
     */
    private function isRelationAnnotation($annotation)
    {
        return preg_match('~@ORM\\\(ManyToOne|OneToOne)\\(~', $annotation->getContent()) === 1;
    }

    /**
     * @param \Symfony\CS\DocBlock\DocBlock   $doc
     * @param \Symfony\CS\DocBlock\Annotation $relationAnnotation
     */
    private function fixRelationAnnotation(DocBlock $doc, Annotation $relationAnnotation)
    {
        $joinColumnAnnotation = $this->findJoinColumnAnnotation($doc);
        if ($joinColumnAnnotation === null) {
            $this->addJoinColumnAnnotation($doc, $relationAnnotation);
        } elseif (preg_match('~(,|\\()\\s*nullable\\s*=~', $joinColumnAnnotation->getContent()) !== 1) {
            $this->extendJoinColumnAnnotation($doc, $joinColumnAnnotation);
        }
    }

    /**
     * @param \Symfony\CS\DocBlock\DocBlock $doc
     *
     * @return \Symfony\CS\DocBlock\Annotation|null
     */
    private function findJoinColumnAnnotation(DocBlock $doc)
    {
        foreach ($doc->getAnnotations() as $annotation) {
            if (preg_match('~@ORM\\\JoinColumn\\(~', $annotation->getContent()) === 1) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * @param \Symfony\CS\DocBlock\DocBlock   $doc
     * @param \Symfony\CS\DocBlock\Annotation $relationAnnotation
     */
    private function addJoinColumnAnnotation(DocBlock $doc, Annotation $relationAnnotation)
    {
        $matches = null;
        preg_match_all('~\\s*\*~', $relationAnnotation->getContent(), $matches);
        $lastLine = $doc->getLine($relationAnnotation->getEnd());
        $lastLine->setContent($lastLine->getContent() . $matches[0][0] . ' @ORM\JoinColumn(nullable=false)' . "\n");
    }

    /**
     * @param \Symfony\CS\DocBlock\DocBlock   $doc
     * @param \Symfony\CS\DocBlock\Annotation $joinColumnAnnotation
     */
    private function extendJoinColumnAnnotation(DocBlock $doc, Annotation $joinColumnAnnotation)
    {
        $firstLine = $doc->getLine($joinColumnAnnotation->getStart());
        if (preg_match('~\\)\\s*$~', $firstLine->getContent()) === 1) {
            $firstLine->setContent(preg_replace('~(@ORM\\\JoinColumn\\()~', '$1nullable=false, ', $firstLine->getContent()));
        } else {
            $matches = null;
            preg_match_all('~\\s*\*~', $joinColumnAnnotation->getContent(), $matches);
            $newText = "\n" . $matches[0][0] . '     nullable=false,';
            $firstLine->setContent(preg_replace('~(@ORM\\\JoinColumn\\()~', '$1' . $newText, $firstLine->getContent()));
        }
    }
}
