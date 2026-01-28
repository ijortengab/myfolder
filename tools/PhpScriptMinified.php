<?php

namespace IjorTengab\MyFolder\Tools;

/**
 *
 * T_ABSTRACT 322
 * T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG 403
 * T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG 404
 * T_AND_EQUAL 358
 * T_ARRAY 341
 * T_ARRAY_CAST 380
 * T_AS 301
 * T_ATTRIBUTE 351
 * T_BAD_CHARACTER 405
 * T_BOOLEAN_AND 365
 * T_BOOLEAN_OR 364
 * T_BOOL_CAST 382
 * T_BREAK 307
 * T_CALLABLE 342
 * T_CASE 304
 * T_CATCH 315
 * T_CLASS 333
 * T_CLASS_C 346
 * T_CLONE 285
 * T_CLOSE_TAG 391
 * T_COALESCE 400
 * T_COALESCE_EQUAL 363
 * T_COMMENT 387
 * T_CONCAT_EQUAL 356
 * T_CONST 312
 * T_CONSTANT_ENCAPSED_STRING 269
 * T_CONTINUE 308
 * T_CURLY_OPEN 396
 * T_DEC 376
 * T_DECLARE 299
 * T_DEFAULT 305
 * T_DIR 345
 * T_DIV_EQUAL 355
 * T_DNUMBER 261
 * T_DO 292
 * T_DOC_COMMENT 388
 * T_DOLLAR_OPEN_CURLY_BRACES 395
 * T_DOUBLE_ARROW 386
 * T_DOUBLE_CAST 378
 * T_DOUBLE_COLON 397
 * T_ECHO 291
 * T_ELLIPSIS 399
 * T_ELSE 289
 * T_ELSEIF 288
 * T_EMPTY 331
 * T_ENCAPSED_AND_WHITESPACE 268
 * T_ENDDECLARE 300
 * T_ENDFOR 296
 * T_ENDFOREACH 298
 * T_ENDIF 290
 * T_ENDSWITCH 303
 * T_ENDWHILE 294
 * T_ENUM 336
 * T_END_HEREDOC 394
 * T_EVAL 274
 * T_EXIT 286
 * T_EXTENDS 337
 * T_FILE 344
 * T_FINAL 323
 * T_FINALLY 316
 * T_FN 311
 * T_FOR 295
 * T_FOREACH 297
 * T_FUNCTION 310
 * T_FUNC_C 349
 * T_GLOBAL 320
 * T_GOTO 309
 * T_HALT_COMPILER 332
 * T_IF 287
 * T_IMPLEMENTS 338
 * T_INC 375
 * T_INCLUDE 272
 * T_INCLUDE_ONCE 273
 * T_INLINE_HTML 267
 * T_INSTANCEOF 283
 * T_INSTEADOF 319
 * T_INTERFACE 335
 * T_INT_CAST 377
 * T_ISSET 330
 * T_IS_EQUAL 366
 * T_IS_GREATER_OR_EQUAL 371
 * T_IS_IDENTICAL 368
 * T_IS_NOT_EQUAL 367
 * T_IS_NOT_IDENTICAL 369
 * T_IS_SMALLER_OR_EQUAL 370
 * T_LINE 343
 * T_LIST 340
 * T_LNUMBER 260
 * T_LOGICAL_AND 279
 * T_LOGICAL_OR 277
 * T_LOGICAL_XOR 278
 * T_MATCH 306
 * T_METHOD_C 348
 * T_MINUS_EQUAL 353
 * T_MOD_EQUAL 357
 * T_MUL_EQUAL 354
 * T_NAMESPACE 339
 * T_NAME_FULLY_QUALIFIED 263
 * T_NAME_QUALIFIED 265
 * T_NAME_RELATIVE 264
 * T_NEW 284
 * T_NS_C 350
 * T_NS_SEPARATOR 398
 * T_NUM_STRING 271
 * T_OBJECT_CAST 381
 * T_OBJECT_OPERATOR 384
 * T_NULLSAFE_OBJECT_OPERATOR 385
 * T_OPEN_TAG 389
 * T_OPEN_TAG_WITH_ECHO 390
 * T_OR_EQUAL 359
 * T_PAAMAYIM_NEKUDOTAYIM 397
 * T_PLUS_EQUAL 352
 * T_POW 401
 * T_POW_EQUAL 402
 * T_PRINT 280
 * T_PRIVATE 324
 * T_PRIVATE_SET PHP Fatal error:  Uncaught Error: Undefined constant "T_PRIVATE_SET" in Command line code:1
 * Stack trace:
 * #0 {main}
 *   thrown in Command line code on line 1
 * T_PROPERTY_C PHP Fatal error:  Uncaught Error: Undefined constant "T_PROPERTY_C" in Command line code:1
 * Stack trace:
 * #0 {main}
 *   thrown in Command line code on line 1
 * T_PROTECTED 325
 * T_PROTECTED_SET PHP Fatal error:  Uncaught Error: Undefined constant "T_PROTECTED_SET" in Command line code:1
 * Stack trace:
 * #0 {main}
 *   thrown in Command line code on line 1
 * T_PUBLIC 326
 * T_PUBLIC_SET PHP Fatal error:  Uncaught Error: Undefined constant "T_PUBLIC_SET" in Command line code:1
 * Stack trace:
 * #0 {main}
 *   thrown in Command line code on line 1
 * T_READONLY 327
 * T_REQUIRE 275
 * T_REQUIRE_ONCE 276
 * T_RETURN 313
 * T_SL 373
 * T_SL_EQUAL 361
 * T_SPACESHIP 372
 * T_SR 374
 * T_SR_EQUAL 362
 * T_START_HEREDOC 393
 * T_STATIC 321
 * T_STRING 262
 * T_STRING_CAST 379
 * T_STRING_VARNAME 270
 * T_SWITCH 302
 * T_THROW 317
 * T_TRAIT 334
 * T_TRAIT_C 347
 * T_TRY 314
 * T_UNSET 329
 * T_UNSET_CAST 383
 * T_USE 318
 * T_VAR 328
 * T_VARIABLE 266
 * T_WHILE 293
 * T_WHITESPACE 392
 * T_XOR_EQUAL 360
 * T_YIELD 281
 * T_YIELD_FROM 282
 *
 */
class PhpScriptMinified
{

    protected $filename;

    protected $lines;

    protected $use_opening_tag = true;

    protected $excluded_string = array();

    /**
     *
     */
    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('File not exists.');
        }
        $this->filename = $filename;
        $lines = self::minified(file_get_contents($this->filename));
        $this->lines = $lines;
        return $this;
    }

    public static function minified($string)
    {
        $tokens = token_get_all($string);

        // Untuk kebutuhan debug.
        $i = 0;

        // Level Struktur.
        $level = 0;
        $lines = array();
        $sentences = array();
        while ($each = array_shift($tokens)) {
            $i++;

            if (is_array($each)) {
                list($code, $string,) = $each;
            }
            else {
                $code = $string = $each;
            }

            do {
                switch ($code) {
                    // 277. Contoh: or
                    case T_LOGICAL_OR:
                    // 260. Contoh: 0
                    case T_LNUMBER:
                    // 262. Contoh: false
                    case T_STRING:
                    // 263. Contoh: \Exception. Contoh: \strlen
                    case T_NAME_FULLY_QUALIFIED:
                    // 265. Contoh: \strlen. Contoh: Template\ConfigReplace
                    case T_NAME_QUALIFIED:
                    // 266. Contoh: $haystack. Contoh: $app
                    case T_VARIABLE:
                    // 268. Contoh: IjorTengab\\MyFolder\\Module\\. Contoh: \\
                    case T_ENCAPSED_AND_WHITESPACE:
                    // 269. Contoh: 'vendor/autoload.php'
                    case T_CONSTANT_ENCAPSED_STRING:
                    // 275. Contoh: require
                    case T_REQUIRE:
                    // 276. Contoh: require_once
                    case T_REQUIRE_ONCE:
                    // 283. Contoh: instanceOf
                    case T_INSTANCEOF:
                    // 284. Contoh: new
                    case T_NEW:
                    // 286. Contoh: die
                    case T_EXIT:
                    // 287. Contoh: if
                    case T_IF:
                    // 288. Contoh: elseif
                    case T_ELSEIF:
                    // 289. Contoh: else
                    case T_ELSE:
                    // 291. Contoh: echo
                    case T_ECHO:
                    // 292. Contoh: do
                    case T_DO:
                    // 293. Contoh: while
                    case T_WHILE:
                    // 295. Contoh: for
                    case T_FOR:
                    // 297. Contoh: foreach
                    case T_FOREACH:
                    // 301. Contoh: as
                    case T_AS:
                    // 302. Contoh: switch
                    case T_SWITCH:
                    // 304. Contoh: case
                    case T_CASE:
                    // 305. Contoh: default
                    case T_DEFAULT:
                    // 307. Contoh: break
                    case T_BREAK:
                    // 308. Contoh: continue
                    case T_CONTINUE:
                    // 310. Contoh: function
                    case T_FUNCTION:
                    // 313. Contoh: return
                    case T_RETURN:
                    // 314. Contoh: try
                    case T_TRY:
                    // 315. Contoh: catch
                    case T_CATCH:
                    // 317. Contoh: throw
                    case T_THROW:
                    // 318. Contoh: use
                    case T_USE:
                    // 321. Contoh: static
                    case T_STATIC:
                    // 326. Contoh: public
                    case T_PUBLIC:
                    // 329. Contoh: unset
                    case T_UNSET:
                    // 330. Contoh: isset
                    case T_ISSET:
                    // 331. Contoh: empty
                    case T_EMPTY:
                    // 339. Contoh: namespace
                    case T_NAMESPACE:
                    // 340. Contoh: list
                    case T_LIST:
                    // 341. Contoh: array
                    case T_ARRAY:
                    // 344. Contoh: __FILE__
                    case T_FILE:
                    // 345. Contoh: __DIR__
                    case T_DIR:
                    // 348. Contoh: __method__
                    case T_METHOD_C:
                    // 352. Contoh: +=
                    case T_PLUS_EQUAL:
                    // 353. Contoh: -=
                    case T_MINUS_EQUAL:
                    // 356. Contoh: .=
                    case T_CONCAT_EQUAL:
                    // 359. Contoh: .=
                    case T_OR_EQUAL:
                    // 364. Contoh: ||
                    case T_BOOLEAN_OR:
                    // 365. Contoh: &&
                    case T_BOOLEAN_AND:
                    // 366. Contoh: ==
                    case T_IS_EQUAL:
                    // 367. Contoh: !=
                    case T_IS_NOT_EQUAL:
                    // 368. Contoh: ===
                    case T_IS_IDENTICAL:
                    // 369. Contoh: !==
                    case T_IS_NOT_IDENTICAL:
                    // 370. Contoh: <=
                    case T_IS_SMALLER_OR_EQUAL:
                    // 371. Contoh: >=
                    case T_IS_GREATER_OR_EQUAL:
                    // 375. Contoh: ++
                    case T_INC:
                    // 376. Contoh: --
                    case T_DEC:
                    // 377. Contoh: (int)
                    case T_INT_CAST:
                    // 379. Contoh: (string)
                    case T_STRING_CAST:
                    // 380. Contoh: (array)
                    case T_ARRAY_CAST:
                    // 382. Contoh: (bool)
                    case T_BOOL_CAST:
                    // 384. Contoh: ->
                    case T_OBJECT_OPERATOR:
                    // 386. Contoh: =>
                    case T_DOUBLE_ARROW:
                    // 397. Contoh: ::
                    case T_DOUBLE_COLON:
                    // 403. Contoh: &
                    case T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG:
                    // Non code.
                    case '=':
                    case '[':
                    case ']':
                    case '.':
                    case '?':
                    case ':':
                    case '+':
                    case '"':
                    case '!':
                    case '=':
                    case ':':
                    case '.':
                    case '[':
                    case ']':
                    case ',':
                    // Contoh: -$needleLength
                    case '-':
                    // Contoh: !function_exists
                    case '!':
                    case '"':
                    case ',':
                    case ':':
                    case '-':
                    case '!':
                    case '=':
                    case '[':
                    case ']':
                    case '.':
                    case '<':
                    case '>':
                    case '*':
                    case '+':
                    case '@':
                    case '^':
                        $sentences[] = $string;
                        break 2;
                }

                // Kode per level.
                switch ($level) {
                    case 0:
                        switch ($code) {
                            case ';':
                                $sentences[] = $string;
                                $lines[] = $sentences;
                                $sentences = array();
                                break 3;
                            case '(':
                                $level++;
                                $sentences[] = $string;
                                break 3;
                            // 396. Contoh: {
                            case T_CURLY_OPEN:
                            case '{':
                                $level++;
                                $sentences[] = $string;
                                break 3;
                            case '}':
                                // $level++;
                                $sentences[] = $string;
                                break 3;
                            // 389
                            case T_OPEN_TAG:
                                $string = rtrim($string);
                                $sentences[] = $string;
                                $lines[] = $sentences;
                                $sentences = array();
                                break 3;
                            // 387
                            case T_COMMENT:
                            // 388
                            case T_DOC_COMMENT:
                                break 3;
                            // 392
                            case T_WHITESPACE:
                                // Cegah '  if ', agar menjadi 'if '.
                                // Cegah ';   if', agar menjadi '; if'
                                if ($count = count($sentences)) {
                                    $index = $count - 1;
                                    $last = $sentences[$index];
                                    if ($last != ' ') {
                                        $sentences[] = ' ';
                                    }
                                }
                                break 3;
                            default:
                                throw new \LogicException('Code '.$code.' is unknown in level '.$level.'. String is: `'.$string.'`.');
                                break 3;
                        }
                        break;
                    case 1:
                        switch ($code) {
                            case ';':
                                $sentences[] = $string;
                                break 3;
                            case '(':
                            // 396. Contoh: {
                            case T_CURLY_OPEN:
                            case '{':
                                $level++;
                                $sentences[] = $string;
                                break 3;
                            case ')':
                                $level--;
                                $sentences[] = $string;
                                break 3;
                            case '}':
                                $level--;
                                $sentences[] = $string;
                                // Register.
                                $lines[] = $sentences;
                                $sentences = array();
                                break 3;
                            // 387
                            case T_COMMENT:
                            // 388
                            case T_DOC_COMMENT:
                                break 3;
                            // 392
                            case T_WHITESPACE:
                                // Cegah '  if ', agar menjadi 'if '.
                                // Cegah ';   if', agar menjadi '; if'
                                if ($count = count($sentences)) {
                                    $index = $count - 1;
                                    $last = $sentences[$index];
                                    if ($last != ' ') {
                                        $sentences[] = ' ';
                                    }
                                }
                                break 3;
                            default:
                                throw new \LogicException('Code '.$code.' is unknown in level '.$level.'. String is: `'.$string.'`.');
                                break 3;
                        }
                        break;
                    default:
                        switch ($code) {
                            case ';':
                                $sentences[] = $string;
                                break 3;
                            case '(':
                            // 396. Contoh: {
                            case T_CURLY_OPEN:
                            case '{':
                                $level++;
                                $sentences[] = $string;
                                break;
                            case ')':
                            case '}':
                                $level--;
                                $sentences[] = $string;
                                break;
                            // 387
                            case T_COMMENT:
                                break 3;
                            // 388
                            case T_DOC_COMMENT:
                            // 392
                            case T_WHITESPACE:
                                // Cegah '  if ', agar menjadi 'if '.
                                // Cegah ';   if', agar menjadi '; if'
                                if ($count = count($sentences)) {
                                    $index = $count - 1;
                                    $last = $sentences[$index];
                                    if ($last != ' ') {
                                        $sentences[] = ' ';
                                    }
                                }
                                break 3;
                            default:
                                // $sentences[] = $string;
                                throw new \LogicException('Code '.$code.' is unknown in level '.$level.'. String is: `'.$string.'`.');
                                break 3;
                        }
                        break;
                }

            }
            while (false);

            // $debugname = 'code';
            // $debugname = 'level';
            // $debugname = 'each';

            // Stopper for debugging.
            if ($i === 91) {
                // $debugname = 'sentences';
                // $debugname = 'lines';
                // break;
                // die('stop');
            }
        }

        $lines[] = $sentences;
        return $lines;
    }

    /**
     *
     */
    public function stripOpeningTag()
    {
        $this->use_opening_tag = false;
        return $this;
    }

    /**
     *
     */
    public function stripFixedString($string)
    {
        $this->excluded_string[] = $string;
        return $this;
    }

    /**
     *
     */
    public function __toString()
    {
        $lines = $this->lines;

        if (!$this->use_opening_tag) {
            array_shift($lines);
        }
        array_walk($lines, function (&$sentences) {
            $sentences = implode('', $sentences);
        });
        if (count($this->excluded_string)) {
            $lines = array_diff($lines, $this->excluded_string);
        }

        // Empty string must skip, caranya:
        // kasih LINE FEED terlebih dahulu di setiap baris.
        array_walk($lines, function (&$sentences) {
            $sentences .= "\n";
        });
        $filtered = array_filter($lines, function ($value) {
            return !($value === "\n");
        });
        $lines = implode('',$filtered);
        // Hapus \n terakhir.
        $lines = substr($lines, 0, -1);
        return $lines;
    }
}
