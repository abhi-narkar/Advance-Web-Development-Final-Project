DELIMITER §
DROP VIEW IF EXISTS CURRENT_ITERATION §

CREATE VIEW CURRENT_ITERATION AS
select id, project_id, min(deadline) as deadline
    from iteration
    where deadline > now()
    group by project_id;

SELECT feature.id,title,functionality,benefit
FROM feature INNER JOIN current_iteration
WHERE feature.project_id = current_iteration.project_id
AND feature.project_id = 1;