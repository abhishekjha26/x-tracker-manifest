<?php


namespace App\Http\Controllers;


use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\License;
use OpenApi\Annotations\Post;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use PHPMailer\PHPMailer\PHPMailer;
use OpenApi\Annotations\Server;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

/**
 * Class AppController
 * @package App\Http\Controllers
 * @OpenApi(
 *     @Info(
 *     version="1.0.0",
 *     title="X Tracker Email Sender",
 *     @License(name="MIT")
 *     ),
 *     @Server(
 *     description="Message API server",
 *     url="http://localhost:8000"
 * )
 * )
 */
class AppController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @Post(
     *  path="/send/email",
     *  summary="Mail sending",
     *  operationId="sendEmail",
     *  @RequestBody(
     *  @MediaType(
     *   mediaType="application/json",
     *     @Schema(
     *      @Property(
     *      property="to",
     *      type="string",
     *      description="The receiver mail address",
     * ),
     *     @Property(
     *      property="subject",
     *      type="string",
     *      description="The subject of the mail",
     * ),
     *     @Property(
     *      property="cotent",
     *      type="string",
     *      description="The content of the mail",
     * ),
     *     @Property(
     *      property="altContent",
     *      type="string",
     *      description="Alternatif content of the mail",
     * ),
     *     example={
     *     "to": "example@example.com",
     *     "subject": "Mailing",
     *     "content": "Content",
     *     "altContent": "Alternatif content"
     * }
     * ),
     * ),
     * ),
     *  @Response(
     *     response="200",
     *     description="Mail sent"
     * ),
     *  @Response(
     *     response="400",
     *     description="messing data"
     * ),
     *  @Response(
     *     response="500",
     *     description="Unexpected error"
     * ),
     *  @Response(
     *     response="default",
     *     description="Unexpected error"
     * )
     * )
     */
    public function sendEmail(Request $request): JsonResponse
    {
        $response = response()->json('Missing fields', 400);

        $to = $request->input('to');
        $subject = $request->input('subject');
        $content = $request->input('content');

        if(Utils::notNull($to, $subject, $content)){
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.strato.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'support@xegit.com';
            $mail->Password   = 'Teranga.DevOps1';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
//            $mail->SMTPDebug = true;

            try {
                $mail->setFrom('support@xegit.com','support@xegit.com');
                $mail->addAddress($to);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = '<!doctype html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                </head>
                <body>
                    '.$content.'
                </body>
                </html>';
                $mail->AltBody = $request->input('altContent') != null ? $request->input('altContent') : strip_tags($content);

                $response = match($mail->send()) {
                    true => response()->json('Mail sent', 200),
                    default => response()->json('An error occurred while sending mail', 400)
                };

            } catch (\Exception $exception) {
                $response = response()->json($exception->getMessage(), 500);
            }
        }
        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Post(
     *     path="/send/sms",
     *     summary="Sms sender",
     *     operationId="SendSms",
     *     @RequestBody(
     *          @MediaType(
     *              mediaType="application/json",
     *              @Schema(
     *                  @Property(
     *                      property="to",
     *                      type="string",
     *                      description="The receiver phone number",
     *                  ),
     *                  @Property(
     *                      property="cotent",
     *                      type="string",
     *                      description="The content of the message",
     *                  ),
     *                  example={
     *                      "to": "+15017122661",
     *                      "content": "Content"
     *                  }
     *              ),
     *          )
     *      ),
     *     @Response(
     *          response="400",
     *          description="messing data"
     *      ),
     *      @Response(
     *          response="500",
     *          description="Unexpected error"
     *      ),
     *      @Response(
     *          response="default",
     *          description="Unexpected error"
     *      )
     * )
     */
    public function sendSms(Request $request): JsonResponse{
        $response = response()->json('Missing fields', 400);
        if(Utils::notNull($request->input('content'), $request->input('to'))){
            $twilio = new Client('AC716aa4085a9147dab1f92288374147cc', '6fde5d2ea9b26a3ee80c03b4f37892ee');

            try {
                $message = $twilio->messages->create(
                    $request->input('to'),
                    [
                        "body" => $request->input('content'),
                        "from" => "+18646598656"
                    ]
                );
                $response = \response()->json('SMS sent');
            } catch (TwilioException $e) {
                $response = \response()->json($e->getMessage());
            }

        }

        return $response;
    }

    public function test() {
        return response()->json('ok', 200);
    }

}
