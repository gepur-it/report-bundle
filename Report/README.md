REPORT creating algorithm
=========================

Generating reports contains three main processes:

1. Creating command where user (or console command) fill request data for report.

2. Push command to rabbit.

3. Listening rabbit and generating Report.

Pushing and listening are the same for all preports and already are realised, so you have to write only CreateCommand, generate() method and action, that contains $handler->push($command).


Detalised algorithm of new report creating (TDD):

1. Write routes in api_swagger for new report.

2. Write actions in controller.

3. Create new directory 'ReportName' in src/ReportBundle/Report.

4. Create here 'NewCommandRepository' if needable to rewrite methods of base class ReportBundle\ReportType\BaseReportCommandRepository

5. Write here 'CreateNewReportCommand' extends AbstractCreateReportCommand.

Command must be a mongo document, that includes (embedded document) or does not include Report (if it is just a slice, for example). Command should have fields with data for generating report.

In annotations (php-docs 'MongoDB\Document') write a repositoryClass="NewCommandRepository" or "BaseReportCommandRepository". Collection name is optional - if you want named mongo-collection different from ReportClassName.

```
/**
 * @MongoDB\Document(
 *     repositoryClass="ReportBundle\ReportType\BaseReportCommandRepository",
 *     collection={"name"="YourReportName"}
 * )
 */
 ```
 
6. Write here 'NewReport' (and its children if need) implements ReportInterface. Report must be a mongo document too. If report is a child of your CreateReportCommand, write in docs:
```
/**
 * @MongoDB\EmbeddedDocument() 
*/
```
7. Write here 'NewReportGenerator' implements ReportGeneratorInterface and write method generate(), how to generate report from command.

8. Write 'services' in src/ReportBundle/Resources/generators.yml with tag 'report_generator' to add commandClass to current generator

9. Write here CommandMeta.php and ReportMeta.php extends stdClass

10. Write 'services' in src/ReportBundle/Resources/types.yml with tag 'report_type' to register new type of reports. Usually write class ReportBundle\ReportType\SimpleReportType for this.

11. If current task contains writing console command for auto-generating report - write it in src/ReportBundle/Command. Then add this command to 'services' in src/ReportBundle/Resources/commands.yml

12. Check working your controller actions via swagger.