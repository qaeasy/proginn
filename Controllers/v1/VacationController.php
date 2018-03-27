<?php

/*
     * @Author: Larry Yang
     * @Created Date: 2016-05-09
     * @Description: Vacation API
     *
     */
namespace hitachi\Phrest\Controllers\v1;

use Phalcon\Mvc\Controller;
use hitachi\Phrest\Models\AmUser;
use hitachi\Phrest\Core\Utils;
use hitachi\Phrest\Models\AtVacationHistory;
use hitachi\Phrest\Transformers\VacationhistoryTransformer;

/**
 * @RoutePrefix("/v1/vacation")
 * @Api(level=1,
 *   limits={
 *   "key" : {
 *   "increment" : "-1 day", "limit" : 1000}
 *   }
 * )
 */
class VacationController extends Controller
{
    /**
     * @Post("/history")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function indexAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $v_history = AtVacationHistory::find(
            array(
                "user_id = '{$user_id}'"
            )
        );

        if(!$v_history){
            return $this->apiResponse->errorUnauthorized();
        }else{
            return $this->apiResponse->withItem($v_history, new VacationhistoryTransformer());
        }        
    }

    /**
     * @Get("/balance")
     * @Post("/balance")
     * @Limit({"increment": "-1 hour", "limit": 50});
     *
     */
    public function balanceAction()
    {
        $user_id = $this->request->getHeader("HTTP_USER_ID");
        $vacation_seconds = AmUser::findFirst(
            array(
                "columns"     => "vacation_seconds",
                "user_id = '{$user_id}'"
            )
        )->vacation_seconds;
        $Utils = new Utils();
        $balance = $Utils->getFormattedTime($vacation_seconds);

        return $this->apiResponse->withArray(["balance" => $balance]);
    }

}

// EOF
