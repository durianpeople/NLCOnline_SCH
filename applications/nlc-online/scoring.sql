SELECT a.nlc_id, IFNULL(benar, 0) benar, IFNULL(salah, 0) salah, IFNULL(benar, 0)*4-IFNULL(salah, 0) score from (
    SELECT nlcid.nlc_id, count(1) benar
    from app_nlc_sesi_user_log x inner join 
        (
            select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log group by user_id, `number`, sesi_id
        ) y
        on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id 
    inner join app_nlc_user_nlc_id nlcid on nlcid.user_id = x.user_id
    inner join app_nlc_sesi ss on x.sesi_id = ss.id
    inner join app_nlc_questions_answerkey qa on ss.questions_id = qa.question_id and qa.`number` = x.`number`
    where qa.answer = x.answer
    group by nlcid.nlc_id
) a left outer join (
    SELECT nlcid.nlc_id, count(1) salah
    from app_nlc_sesi_user_log x inner join 
        (
            select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log group by user_id, `number`, sesi_id
        ) y
        on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id 
    inner join app_nlc_user_nlc_id nlcid on nlcid.user_id = x.user_id
    inner join app_nlc_sesi ss on x.sesi_id = ss.id
    inner join app_nlc_questions_answerkey qa on ss.questions_id = qa.question_id and qa.`number` = x.`number`
    where qa.answer <> x.answer
    group by nlcid.nlc_id
) b
on a.nlc_id = b.nlc_id
union
SELECT a.nlc_id, IFNULL(benar, 0) benar, IFNULL(salah, 0) salah, IFNULL(benar, 0)*4-IFNULL(salah, 0) score from (
    SELECT nlcid.nlc_id, count(1) salah
    from app_nlc_sesi_user_log x inner join 
        (
            select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log group by user_id, `number`, sesi_id
        ) y
        on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id 
    inner join app_nlc_user_nlc_id nlcid on nlcid.user_id = x.user_id
    inner join app_nlc_sesi ss on x.sesi_id = ss.id
    inner join app_nlc_questions_answerkey qa on ss.questions_id = qa.question_id and qa.`number` = x.`number`
    where qa.answer <> x.answer
    group by nlcid.nlc_id
) a left outer join (
    SELECT nlcid.nlc_id, count(1) benar
    from app_nlc_sesi_user_log x inner join 
        (
            select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log group by user_id, `number`, sesi_id
        ) y
        on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id 
    inner join app_nlc_user_nlc_id nlcid on nlcid.user_id = x.user_id
    inner join app_nlc_sesi ss on x.sesi_id = ss.id
    inner join app_nlc_questions_answerkey qa on ss.questions_id = qa.question_id and qa.`number` = x.`number`
    where qa.answer = x.answer
    group by nlcid.nlc_id
) b
on a.nlc_id = b.nlc_id
order by score;
