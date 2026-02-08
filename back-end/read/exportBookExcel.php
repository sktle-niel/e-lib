<?php
include '../../auth/sessionCheck.php';
include '../../config/connection.php';
include 'readLibBooks.php';

// Fetch all data for export
$libBooks = getAllLibBooksForExport();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="library_books.xls"');

// Output HTML with Excel-specific XML namespaces and print settings
echo '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">

 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Library System</Author>
  <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
 </DocumentProperties>

 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>12000</WindowHeight>
  <WindowWidth>15000</WindowWidth>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>

 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" ss:Size="11"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>

  <Style ss:ID="Title">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Calibri" ss:Size="18" ss:Bold="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
  </Style>

  <Style ss:ID="Header">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1"/>
   <Interior ss:Color="#D9D9D9" ss:Pattern="Solid"/>
  </Style>

  <Style ss:ID="Data">
   <Alignment ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10"/>
  </Style>

  <Style ss:ID="DataCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10"/>
  </Style>
 </Styles>

 <Worksheet ss:Name="Library Books">

  <Table>
   <Column ss:Width="300"/>
   <Column ss:Width="200"/>
   <Column ss:Width="120"/>

   <!-- Title Row -->
   <Row ss:Height="30">
    <Cell ss:MergeAcross="2" ss:StyleID="Title">
     <Data ss:Type="String">Library Books</Data>
    </Cell>
   </Row>

   <!-- Empty Row for spacing -->
   <Row ss:Height="10">
    <Cell><Data ss:Type="String"></Data></Cell>
    <Cell><Data ss:Type="String"></Data></Cell>
    <Cell><Data ss:Type="String"></Data></Cell>
   </Row>

   <!-- Header Row -->
   <Row ss:Height="40">
    <Cell ss:StyleID="Header"><Data ss:Type="String">Book Title</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Author</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">Publish Date</Data></Cell>
   </Row>';

foreach ($libBooks as $book) {
    echo '
   <Row>
    <Cell ss:StyleID="Data"><Data ss:Type="String">' . htmlspecialchars($book['book_title']) . '</Data></Cell>
    <Cell ss:StyleID="Data"><Data ss:Type="String">' . htmlspecialchars($book['author']) . '</Data></Cell>
    <Cell ss:StyleID="DataCenter"><Data ss:Type="String">' . date('M d, Y', strtotime($book['publish_date'])) . '</Data></Cell>
   </Row>';
}

echo '
  </Table>

  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Layout x:Orientation="Portrait"/>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3" x:Data="Page &amp;P of &amp;N"/>
    <PageMargins x:Bottom="0.75" x:Left="0.5" x:Right="0.5" x:Top="0.75"/>
   </PageSetup>
   <FitToPage/>
   <Print>
    <FitWidth>1</FitWidth>
    <FitHeight>0</FitHeight>
    <ValidPrinterInfo/>
    <Scale>100</Scale>
    <HorizontalResolution>600</HorizontalResolution>
    <VerticalResolution>600</VerticalResolution>
   </Print>
   <Selected/>
   <DoNotDisplayGridlines/>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>

 </Worksheet>
</Workbook>';

exit();
?>
