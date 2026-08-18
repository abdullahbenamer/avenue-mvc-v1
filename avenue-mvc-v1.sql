-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2026 at 05:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `avenue-mvc-v1`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL,
  `cat_code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `cat_code`) VALUES
(1, 'MANAGEMENT', 'M'),
(2, 'INFORMATION TECHNOLOGY', 'T'),
(3, 'ENGINEERING', 'E'),
(4, 'FINANCE AND ACCOUNTING', 'F'),
(5, 'CAPACITY BUILDING', 'C'),
(6, 'SECURITY AND SAFETY', 'S'),
(7, 'PUBLIC AND POLITICS', 'P'),
(8, 'MEDICAL AND HEALTH', 'H'),
(9, 'OIL & GAS Specific', 'O');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `cert_id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `cert_date` timestamp NULL DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `ven_id` int(11) DEFAULT NULL,
  `cert_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(255) NOT NULL,
  `count_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`city_id`, `city_name`, `count_id`) VALUES
(1, 'ISTANBUL', 1),
(2, 'TRIPOLI', 3),
(3, 'BENGHAZI', 3),
(4, 'DUBAI', 5),
(5, 'SALALAH', 4),
(6, 'MUSCAT', 4),
(7, 'CAIRO', 2),
(8, 'ALEXANDRIA ', 2),
(9, 'ABU DHABI', 5),
(10, 'OMAN', 4),
(11, 'BONN', 6),
(12, 'BERLIN', 6),
(13, 'TUNIS', 8),
(14, 'GAZA', 11),
(15, 'QUDS', 11),
(16, 'AMMAN', 7),
(17, 'Riyadh', 12),
(18, 'Jeddah', 12),
(19, 'ALKHUMS', 3);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `count_id` int(11) NOT NULL,
  `count_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`count_id`, `count_name`) VALUES
(1, 'TURKEY'),
(2, 'EGYPT'),
(3, 'LIBYA'),
(4, 'OMAN'),
(5, 'UNITED ARAB EMIRATES'),
(6, 'GERMANY'),
(7, 'JORDAN'),
(8, 'TUNISIA'),
(9, 'QATAR'),
(11, 'PALESTINE'),
(12, 'SAUDI ARABIA');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_title` varchar(255) NOT NULL,
  `course_title_a` varchar(255) DEFAULT NULL,
  `course_duration` int(11) DEFAULT 10,
  `course_uod` enum('HOURS','DAYS','WEEKS','MONTHS','NONE') NOT NULL DEFAULT 'DAYS',
  `week` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `cat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All courses we provide over the history of our business';

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_title`, `course_title_a`, `course_duration`, `course_uod`, `week`, `month`, `cat_id`) VALUES
(1, 'LEADERSHIP AND STRATEGIC MANAGEMENT', 'القيادة والاإدارة الاسترتيجية', 10, 'DAYS', 2, 0, 1),
(2, 'MODERN METHODS & TECHNIQUES TO PROTECT AGAINST CORROSION', 'الطرق والتقنيات الحديثة للحماية من التأكل', 5, 'DAYS', 1, 0, 3),
(3, 'HUMAN RESOURCE MANAGEMENT', 'إدارة الموارد البشرية', 5, 'DAYS', 1, 0, 1),
(4, 'MIDDLE AND SUPERVISORY ADMINISTRATION PROGRAM', 'برنامج الإدارة الوسطى والإشرافية', 10, 'DAYS', 2, 0, 1),
(5, 'PROJECT MANAGEMENT PROFESSIONAL', 'محترف إدارة المشروعات', 10, 'DAYS', 2, 0, 1),
(7, 'PRODUCTION OPERATIONS', 'عمليات الانتاج', 10, 'DAYS', 2, 0, 3),
(8, 'MODERN MANAGEMENT', 'الادارة الحديثة', 5, 'DAYS', 1, 0, 1),
(9, 'MICROSOFT POWER BI DATA ANALYST', 'محلل بيانات ميكروسوفت باور بي اي', 10, 'DAYS', 2, 0, 2),
(12, 'LEADERSHIP AND STRATEGY FORESIGHT', 'القيادة والإستشراف الاسترتيجي', 10, 'DAYS', 2, 0, 1),
(14, 'BASIC BUDGETING FOR NON-FINANCIAL PROFISSIONALS', 'أساسيات الميزانية للمحترفين غير الماليين', 5, 'DAYS', 1, 0, 4),
(15, 'FULL FINANCIAL CYCLE IN OIL & GAS', 'الدورة المالية الكاملة في قطاع النفط والغاز', 5, 'DAYS', 1, 0, 4),
(19, 'REFINERIES OPERATIONS & MARKETING', 'عمليات المصافي والتسويق', 260, 'DAYS', 52, 12, 3),
(20, 'PRODUCTION OPERATIONS OF NATURAL GAS', 'عمليات انتاج الغاز الطبيعي', 5, 'DAYS', 1, 0, 3),
(21, 'FUNDAMENTALS OF MANAGEMENT SKILLS AND COMMUNICATION AT WORK', 'أساسيات مهارات الإدارة والتواصل في العمل', 5, 'DAYS', 1, 0, 5),
(22, 'QUALITY MANAGEMENT', 'إدارة الجودة', 5, 'DAYS', 1, 0, 1),
(23, 'GENERAL VOIP COMMUNICATION', 'التواصل عبر الإنترنت باستخدام تقنية VOIP', 5, 'DAYS', 1, 0, 2),
(24, 'FIREFIGHTING SYSTEM DESIGN', 'تصميم أنظمة مكافحة الحرائق', 5, 'DAYS', 1, 0, 6),
(25, 'MICROSOFT WINDOWS SERVER', 'خادم ميكروسوفت ويندوز', 5, 'DAYS', 1, 0, 2),
(29, 'BOARD LEADERSHIP MASTERCLASS', 'ماستر كلاس في قيادة مجالس الإدارة', 5, 'DAYS', 1, 0, 1),
(30, 'LEADERSHIP', 'برنامج القيادة', 5, 'DAYS', 1, 0, 1),
(31, 'OPERATION, DIAGNOSTICS & MAINTENANCE OF EQUIPMENT FOR OIL & GAS PRODUCTION', 'تشغيل وتشخيص وصيانة معدات إنتاج النفط         والغاز', 10, 'DAYS', 2, 0, 3),
(32, 'MANAGEMENT SKILLS-TEAM LEADERSHIP SKILLS MASTERCLASS', 'مهارات الإدارة - دورة تدريبية متقدمة في مهارات قيادة فرق العمل', 5, 'DAYS', 1, 0, 1),
(36, 'CEMENTING, ACIDIZING, GAS LIFT DESIGN, ESP DESIGN AND EQUIPMENT, WELL COMPLETION', 'التصميم للحقن الإسمنتي، التحميض، رفع بالغاز، معدات المضخات الغاطسة ، وإكمال الآبار', 10, 'DAYS', 2, 0, 3),
(37, 'MECHANICAL MAINTENANCE', 'الصيانة الميكانيكية', 10, 'DAYS', 2, 0, 3),
(38, 'STRATEGIC MANAGEMENT', 'الادارة الاسترتيجية', 10, 'DAYS', 2, 0, 1),
(39, 'MECHANICAL MAINTENANCE STRATEGIES AND OBJECTIVES', 'استراتيجيات وأهداف الصيانة الميكانيكية', 10, 'DAYS', 2, 0, 3),
(40, 'PRECISION MACHINERY MAINTENANCE ', 'صيانة الالات الدقيقة', 10, 'DAYS', 2, 0, 3),
(41, 'DOCUMENT CONTROL SYSTEM (EDMS)', 'نظم إدارة المستندات', 10, 'DAYS', 2, 0, 2),
(43, 'TOTAL QUALITY MANAGEMENT (TQM) FOR ENTERPRISES', 'إدارة الجودة الشاملة للمؤسسات', 10, 'DAYS', 2, 0, 1),
(44, 'HOSPITALITY & CUSTOMER SERVICES', 'خدمات الزبائن والضيافة', 10, 'DAYS', 2, 0, 1),
(45, 'DOWNSTREAM REGULATIONS & MIDSTREAM OIL & GAS FUNDAMENTALS ', 'اللوائح التنظيمية لقطاع المصب وأساسيات قطاع النفط والغاز الوسيط', 10, 'DAYS', 2, 0, 9),
(46, 'EXECUTIVE LEADERSHIP & MANAGEMENT SKILLS', 'مهارات القيادة التنفيذية والإدارية', 10, 'DAYS', 2, 0, 1),
(47, 'LOGISTICS AND GOODS SERVICES', 'الخدمات اللوجستية والبضائع', 10, 'DAYS', 2, 0, 5),
(48, 'RISK MANAGEMENT ', 'ادارة المخاطر', 10, 'DAYS', 2, 0, 1),
(49, 'STRATEGIC PLANNING', 'التخطيط الاسترتيجي', 10, 'DAYS', 2, 0, 1),
(50, 'TOTAL QUALITY MANAGEMENT (TQM)', 'إدارة الجودة الشاملة', 10, 'DAYS', 2, 0, 1),
(51, 'HR POLICIES & PROCEDURES', 'إجراءات وسياسات الموارد البشرية', 10, 'DAYS', 2, 0, 1),
(52, 'ADVANCED TEAM MANAGEMENT AND SUPERVISING SKILLS', 'مهارات متقدمة في إدارة الفرق والإشراف', 10, 'DAYS', 2, 0, 1),
(53, 'LOGISTICS TEAM IN THE OIL AND GAS INDUSTRY', 'فرق الدعم اللوجستي في قطاع النفط', 10, 'DAYS', 2, 0, 9),
(54, 'ADVANCED COMMUNICATION SKILLS FOR DEAF AND MUTE INDIVIDUALS ', 'مهارات التواصل المتقدمة للأفراد الصم والبكم', 10, 'DAYS', 2, 0, 5),
(55, 'WAREHOUSING AND MATERIAL MANAGEMENT', 'المخازن وادارة المواد', 5, 'DAYS', 1, 0, 1),
(56, 'PREPARATION, ANALYSIS, AND EVALUATION OF FEASIBILITY STUDIES', 'إعداد وتحليل وتقييم دراسات الجدوى', 5, 'DAYS', 1, 0, 5),
(57, 'PROCUREMENT AND SUPPLY CHAIN (5)', 'التوريد وسلاسل الامداد', 5, 'DAYS', 1, 0, 1),
(58, 'INSPECTION, REPAIR & MAINTENANCE OF CRUDE OIL STORAGE TANKS', 'فحص وصيانة وإصلاح خزانات النفط الخام', 5, 'DAYS', 1, 0, 7),
(81, 'HUMAN RESOURCES DIPLOMA', 'دبلومة الموارد البشرية', 260, 'DAYS', 52, 12, 1),
(82, 'PMP TRAINING', 'كورس محترف ادارة المشروعات', 5, 'DAYS', 1, 0, 1),
(83, 'THE ROLE OF PROFESSIONAL UNIONS IN OIL COMPANIES & ORGANIZATIONS', 'دور النقابات المهنية في الشركات والمؤسسات النفطية', 10, 'DAYS', 2, 0, 7),
(84, 'QUANTITATIVE SEISMIC INTERPRETATION', 'التفسير الكمي لبيانات الزلازل', 10, 'DAYS', 2, 0, 3),
(85, 'ADVANCED SEISMIC DATA ACQUISITION', 'الاكتساب المتقدم لبيانات الزلازل', 10, 'DAYS', 2, 0, 3),
(86, 'FORMATION EVALUATION FOR OIL & GAS', 'تقييم التكوينات في النفط والغاز', 10, 'DAYS', 2, 0, 3),
(87, 'PRINCIPLES OF SEISMIC INTERPRETATION', 'مبادئ تفسيرالبيانات الزلزالية', 10, 'DAYS', 2, 0, 3),
(88, 'WELLSITE GEOLOGY & OPERATIONS', 'عمليات وجيولوجيا مواقع الأبار', 10, 'DAYS', 2, 0, 3),
(89, 'ELECTRICAL MAINTENANCE', 'الصيانة الكهربائية', 10, 'DAYS', 2, 0, 3),
(90, 'GENERAL MAINTENANCE, WORKSHOPS, AND PRODUCTION LINE MAINTENANCE', 'الصيانة العامة وورش العمل وصيانة خطوط الإنتاج', 10, 'DAYS', 2, 0, 3),
(96, 'HEALTH MANAGEMENT IN OILFIELD SITES', 'الالدارة الصحية في المواقع النفطية (10 DAYS)', 10, 'DAYS', 2, 0, 5),
(98, 'INDUSTRIAL SECURITY', 'الامن الصناعي', 10, 'DAYS', 2, 0, 5),
(99, 'Developing creativity and supervision skills in the work', 'تنمية مهارات الابداع والاشراف في بيئة العمل (5 DAYS)', 5, 'DAYS', 1, 0, 5),
(100, 'PROJECT MANAGEMENT', 'إدارة المشروعات', 10, 'DAYS', 2, 0, 1),
(101, 'PURCHASING PROCUREMENT AND SUPPLY CHAIN', 'المشتريات وسلاسل التوريد', 10, 'DAYS', 2, 0, 5),
(112, 'INTEGRATED COMPREHENSIVE ELECTRICAL, MECHANICAL, AND MAINTENANCE', 'الصيانة المتكاملة والشاملة الكهربائية والميكانيكية', 10, 'DAYS', 2, 0, 3),
(125, 'MICROSOFT OFFICE 365', 'ميكروسوفت اوفيس 365', 10, 'DAYS', 2, 0, 2),
(128, 'MICROSOFT SQL SERVER', 'خادم قواعد البيانات من ميكروسوفت', 10, 'DAYS', 2, 0, 2),
(133, 'DETECT AND PREVENT THE EMPLOYEES FRAUD', 'كشف ومنع الاحتيال الوظيفي', 5, 'DAYS', 1, 0, 5),
(134, 'TEACHING AIDS & eLEARNING', 'الوسائل التعليمية والتعليم اولاين', 10, 'DAYS', 2, 0, 5),
(135, 'NEGOTIATION SKILLS AND MANAGEMENT', 'مهارات التفاوض والادارة', 5, 'DAYS', 1, 0, 1),
(136, 'TRAINING NEEDS', 'الاحتياجات التدربية', 10, 'DAYS', 2, 0, 5),
(137, 'HUMAN RESOURCES MANAGEMENT', 'إدارة الموارد البشرية', 10, 'DAYS', 2, 0, 1),
(138, 'INVENTORY CONTROL AND ASSETS', 'مراقبة الاصول والجرد', 5, 'DAYS', 1, 0, 5),
(140, 'STRATEGIC METHODS FOR MANAGEMENT AND MARKETING', 'الطرق الاستراتيجية في في الادارة والتسويق', 10, 'DAYS', 2, 0, 5),
(141, 'STRATEGIC LEADER PLANNING  NEGOTIATION & CONFLICT MANAGEMENT', 'تخطيط القائد الاسترتيجي، التفاوض وادارة الخلافات', 10, 'DAYS', 2, 0, 1),
(142, 'EFFECTIVE PAYROLL MANAGEMENT & CONTROL', 'الإدارة الفعّالة للرواتب والمراقبة', 5, 'DAYS', 1, 0, 4),
(143, 'LEGAL TRANSLATION', 'الترجمة القانونية', 10, 'DAYS', 2, 0, 5),
(144, 'ISO 14001', 'نظام أيزو', 10, 'DAYS', 2, 0, 1),
(145, 'PROCESS ENGINEERING', 'هندسة العمليات', 10, 'DAYS', 2, 0, 3),
(146, 'ASPEN HYSYS', 'برنامج أسبن هايسيس', 10, 'DAYS', 2, 0, 3),
(147, 'Financial Audit, Project Evaluation and Payment Mechanisms', 'المراجعة المالية وتقييم المشاريع وآليات الدفع (10 DAYS)', 10, 'DAYS', 2, 0, 4),
(148, 'Economic Planning, Project Evaluation, Audit Strategies and Financial Reimbursement', 'التخطيط الاقتصادي وتقييم المشاريع واستراتيجيات المراجعة والتسديد المالي', 10, 'DAYS', 2, 0, 4),
(149, 'Economic Studies, Project Evaluation, Financial Audit and Payment Mechanisms', 'الدراسات الاقتصادية وتقييم المشاريع والمراجعة المالية وآليات الدفع', 10, 'DAYS', 2, 0, 4),
(150, 'FIRE FIGHTING MANAGEMENT', 'إدارة مكافحة الحرائق', 10, 'DAYS', 2, 0, 1),
(151, 'MICROSOFT BOOTCAMP', 'برنامج ميكروسوفت الشامل', 130, 'DAYS', 26, 6, 2),
(152, 'OIL EXPORT SHIPMENT DOCUMENTATION AND TRACKING', 'توثيق وتتبع شحنات تصدير النفط', 130, 'DAYS', 26, 6, 9),
(153, 'MODERN MANAGEMENT SKILLS', 'مهارات الإدارة الحديثة', 80, 'DAYS', 16, 4, 1),
(155, 'ADVANCED MECHANICAL MAINTENANCE', 'الصيانة الميكانيكية المتقدمة', 80, 'DAYS', 16, 4, 3),
(156, 'ELECTRICAL MAINTENANCE (LT)', 'الصيانة الكهربائية', 80, 'DAYS', 16, 4, 3),
(157, 'SUPPLY CHAIN MANAGEMENT', 'إدارة سلاسل التوريد', 130, 'DAYS', 26, 6, 1),
(158, 'INDUSTRIAL SECURITY AND MODERN SECURITY SYSTEMS', 'الأمن الصناعي و النظم الامنية الحديثة', 130, 'DAYS', 26, 6, 5),
(159, 'PREPARING & WRITING REPORTS IN ARABIC', 'إعداد وكتابة التقارير بالعربية', 10, 'DAYS', 2, 0, 5),
(160, 'ARCHIVING', 'ألأرشفة', 10, 'DAYS', 2, 0, 5),
(161, 'CONCRETE TECHNOLOGY AND APPLICATIONS', 'تقنيات وتطبيقات الخرسانة', 10, 'DAYS', 2, 0, 5),
(162, 'CONSTRUCTION PROJECT MANAGEMENT', 'إدارة مشاريع البناء', 10, 'DAYS', 2, 0, 1),
(163, 'CONSTRUCTION RISK MANAGEMENT', 'إدارة مخاطر البناء', 10, 'DAYS', 2, 0, 1),
(165, 'COST ESTIMATION & BUDGETING IN CIVIL ENGINEERING', 'تقدير التكلفة ووضع الميزانيات في الهندسة المدنية', 10, 'DAYS', 2, 0, 3),
(166, 'MODERN OFFICES ADMINISTRATION', 'إدارة المكاتب الحديثة', 10, 'DAYS', 2, 0, 5),
(169, 'CYPER SECURITY', 'الأمن السيبراني', 10, 'DAYS', 2, 0, 2),
(170, 'OPERATION AND MAINTENANCE', 'التشغيل والصيانة', 10, 'DAYS', 2, 0, 3),
(171, 'OPERATION OF REFINERIES WITH DCS SYSTEMS & RCC OPERATIONS', 'تشغيل المصافي باستخدام أنظمة DCS وعمليات RCC', 10, 'DAYS', 2, 0, 9),
(172, 'DISTRIBUTED CONTROL SYSTEM (DCS)', 'نظام التحكم الموزع', 10, 'DAYS', 2, 0, 5),
(173, 'PROJECT MANAGEMENT SKILLS', 'مهارات إدارة المشروعات', 10, 'DAYS', 2, 0, 1),
(174, 'CONSTRUCTION COST ESTIMATION & COST CONTROL', 'تقدير تكاليف البناء وإدارة التكلفة', 5, 'DAYS', 1, 0, 3),
(175, 'REVIT FOR CIVIL AND ARCHITECTURE ENGINEERS', 'ريفيت للمهندسين المدنيين والمعماريين', 5, 'DAYS', 1, 0, 3),
(176, 'AUTOCAD FOR CIVIL AND ARCHITECTURE ENGINEERS', 'أوتوكاد لمهندسي المدني والمعماري', 5, 'DAYS', 1, 0, 3),
(177, 'TECHNICAL REPORT', 'التقارير الفنية', 10, 'DAYS', 2, 0, 5),
(178, 'WORK ORGANIZATION SKILLS', 'مهارات تنظيم العمل', 10, 'DAYS', 2, 0, 5),
(179, 'ADMINISTRATIVE SKILLS IN PLANNING AND MONITORING', 'المهارات الإدارية في التخطيط والمتابعة', 10, 'DAYS', 2, 0, 5),
(180, 'HUMAN RESOURCES IN WORKFORCE STAFFING', 'ادارة الموارد البشرية والملاكات الوظيفية', 10, 'DAYS', 2, 0, 5),
(182, 'PROCUREMENT, CUSTOMS CLEARANCE, AND AIRCRAFT SPARE PARTS SHIPPING', 'المشتريات والتسريح الجمركي وشحن قطع غيار الطائرات (5 DAYS)', 5, 'DAYS', 1, 0, 5),
(183, 'AUDIT PRACTICES IN BANKING', 'مماراسات التدقيق في المصارف', 10, 'DAYS', 2, 0, 5),
(184, 'CREDIT MANAGEMENT', 'إدارة الأئتمان', 10, 'DAYS', 2, 0, 1),
(186, 'SHARI’AH AUDIT IN BANKING', 'التدقيق الشرعي في المصارف', 10, 'DAYS', 2, 0, 5),
(187, 'PREPARING FINANCIAL REPORTS', 'إعداد التقارير المالية', 10, 'DAYS', 2, 0, 4),
(188, 'ROAD SAFETY', 'السلامة في الطرق', 10, 'DAYS', 2, 0, 6),
(189, 'QHSE KPO’S AND BALANCED SCORECARD', 'مؤشرات الأداء الرئيسية في الجودة والسلامة والصحة والبيئة (QHSE) ولوحة القياس المتوازن', 10, 'DAYS', 2, 0, 5),
(190, 'TRANSPORTATION OF DANGEROUS GOODS', 'نقل المواد الخطرة', 10, 'DAYS', 2, 0, 5),
(191, 'SAFE FORKLIFT OPERATIONS & MAINTENANCE', 'التشغيل الامن وصيانة الروافع الشوكية', 10, 'DAYS', 2, 0, 6),
(192, 'OFFICE MANAGEMENT EXCELLENCE', 'التميز في ادارة المكاتب', 10, 'DAYS', 2, 0, 1),
(193, 'WAREHOUSE MANAGEMENT AND INVENTORY CLASSIFICATION OPERATIONS', 'إدارة المخازن وعمليات تصنيف المخزون', 10, 'DAYS', 2, 0, 5),
(195, 'ADVANCED SYSTEMS FOR WAREHOUSE MANAGEMENT AND INVENTORY ANALYSIS AND CONTROL', 'أنظمة متقدمة لإدارة المستودعات وتحليل المخزون والتحكم فيه', 10, 'DAYS', 2, 0, 1),
(196, 'EXCELLENCE IN WAREHOUSE AND INVENTORY', 'التميز في المخازن وادارة المخزون', 10, 'DAYS', 2, 0, 5),
(197, 'PROCESS PLANT START-UP, COMMISSIONING & TROUBLESHOOTING', 'التشغيل التجريبي والإقلاع ومعالجة مشاكل المنشأت النفطية', 10, 'DAYS', 2, 0, 5),
(198, 'NEBOSH CERTIFICATE IN FIRE SAFETY & NEBOSH ENVIRONMENTAL MANAGEMENT CERTIFICATE', 'شهادة نيبوش في ادارة السلامة والبيئة', 10, 'DAYS', 2, 0, 1),
(199, 'OIL & GAS LABORATORY MANAGEMENT/HPLC: OPERATION, CALIBRATION AND TROUBLESHOOTING (WORKSHOP)', 'ورشة عمل إدارة وتشغيل وصيانة ومعايرة وحل مشاكل مختبرات النفط والغاز', 10, 'DAYS', 2, 0, 1),
(200, 'ESSENTIAL SKILLS FOR OIL & GAS MANAGERS & SUPERVISORS', 'المهارات الأساسية لمديري ومشرفي قطاع النفط والغاز (10 DAYS)', 10, 'DAYS', 2, 0, 5),
(201, 'RISK BASED PROCESS MANAGEMENT IN OIL & GAS INDUSTRY', 'إدارة العمليات المعتمدة على المخاطر في صناعة النفط والغاز', 10, 'DAYS', 2, 0, 1),
(202, 'DRILLING RIG AUDIT & INSPECTION', 'تدقيق وتفتيش أجهزة الحفر', 10, 'DAYS', 2, 0, 5),
(203, 'MICROSOFT AZURE FUNDAMENTALS', 'اساسيات الخدمات السحابية (أزور) من ميكروسوفت', 10, 'DAYS', 2, 0, 2),
(204, 'PROJECT MANAGEMENT USING INFORMATION TECHNOLOGY AND ARTIFICIAL INTELLIGENCE', 'ادارة المشروعات باستخدام التقنيات الرقمية والذكاء الاصطناعي (5 DAYS)', 5, 'DAYS', 1, 0, 1),
(205, 'RESERVOIR, GEOLOGY AND PRODUCTION ENGINEERING', 'المكامن والجيولوجيا وهندسة الانتاج', 130, 'DAYS', 26, 6, 3),
(207, 'ELELCTRONIC ARCHIVING', ' الارشفة الالكترونية', 10, 'DAYS', 2, 0, 5),
(208, 'PLC MAINTENANCE, PROGRAMMING, AND INSTRUMENTATION SYSTEMS', 'صيانة وبرمجة أنظمة التحكم المنطقي وأنظمة التشغيل الكهربائية والالات الدقيقة (10 DAYS)', 10, 'DAYS', 2, 0, 3),
(209, 'AIR COMPRESSORS AND FLOW METER CALIBRATION IN OIL FACTORIES', 'تشغيل ومتابعة ضواغط الهواء وتشغيل ومراقبة ومعايرة عدادات التدفق في مصانع الزيوت', 10, 'DAYS', 2, 0, 3),
(210, 'BASE OIL TANK AND ADDITIVE SYSTEM OPERATIONS', 'تشغيل خزانات الزيوت الأساسية والاضافات الكيميائية', 10, 'DAYS', 2, 0, 3),
(211, 'OPERATION AND MAINTENANCE OF PLASTIC BOTTLES PRODUCTION LINES', 'تشغيل و متابعة صيانة خطوط انتاج العلب البلاستيكية (10 DAYS)', 10, 'DAYS', 2, 0, 3),
(212, 'PACKAGING LINE CONTROL SYSTEMS AND FINAL PRODUCT SCHEDULING', 'أنظمة التحكم في عمليات تشغيل ومتابعة وتجهيز خطوط التغليف والمنتج النهائي وجدولة الأصناف (10 DAYS)', 10, 'DAYS', 2, 0, 3),
(213, 'WAREHOUSES SYSTEMS AND INVENTORY CONTROL', 'المنظومات التخزينية والرقابة علي المخزون', 10, 'DAYS', 2, 0, 3),
(216, 'STATISTICAL INDICATORS, LEADERSHIP SKILLS, AND PROJECT MANAGEMENT', 'المؤشرات الإحصائية والمهارات القيادية وإدارة المشاريع', 10, 'DAYS', 2, 0, 1),
(217, 'Statistical Packages for Social Science (SPSS)', 'حزم التحليل الإحصائي للعلوم الاجتماعية (SPSS)', 10, 'DAYS', 2, 0, 5),
(218, 'ENGINEERING SUPERVISION AND EXECUTION PRINCIPLES IN PROJECTS', 'مبادئ الإشراف والتنفيذ الهندسي', 10, 'DAYS', 2, 0, 3),
(219, 'ENGINEERING PROJECT MANAGEMENT', 'إدارة المشاريع الهندسية', 10, 'DAYS', 2, 0, 3),
(220, 'SKETCHUP V-RAY WORKSHOP', 'ورشة عمل سكتش اب للتصميم الهندسي', 5, 'DAYS', 1, 0, 3),
(222, 'THE INDUSTRIAL SECURITY', 'الأمن الصناعي', 5, 'DAYS', 1, 0, 6),
(223, 'SOFT SKILLS FOR TECHNICIANS', 'المهارات الشخصية للفنيين', 5, 'DAYS', 1, 0, 5),
(224, 'IMPROVING COMMUNICATION, PRIORITIZATION, AND TIME MANAGEMENT', 'تحسين الأتصالات وتحديد الأولويات وإدارة الوقت', 5, 'DAYS', 1, 0, 5),
(226, 'WORKSHOP OF IMPROVING COMMUNICATION, PRIORITIZATION, & TIME MANAGEMENT', 'ورشة عمل تحسين الأتصالات وتحدي الأولويات وإدارة الوقت', 5, 'DAYS', 1, 0, 5),
(227, 'SURVEYING AND GEOMATICS', 'المساحة والرفع المساحي', 5, 'DAYS', 1, 0, 3),
(228, 'TIME MANAGEMENT SKILLS, ORGANIZATION, AND MANAGING WORK STRESS', 'مهارات إدارة الوقت، التنظيم، وإدارة ضغط العمل', 20, 'DAYS', 4, 1, 1),
(229, 'ENTERNAL PROTECTION', 'الحماية الداخلية', 5, 'DAYS', 1, 0, 5),
(230, 'CORROSION AND NON-DESTRUCTIVE TESTING (NDT)', 'التآكل والاختبارات غير التدميرية (NDT)', 5, 'DAYS', 1, 0, 5),
(231, 'PAINTING', 'الحماية من التأكل - الطلاء', 5, 'DAYS', 1, 0, 5),
(232, 'WELDING TECHNIQUES', 'تقنيات اللحام', 5, 'DAYS', 1, 0, 5),
(234, 'WORKSHOP IN MODERN COMMUNICATION', 'ورشة عمل الاتصالات الحديثة', 5, 'DAYS', 1, 0, 5),
(235, 'ENVIRONMENT MONITORING & IMPACT ASSESSMENT', 'المراقبة البيئية وتقييم الأثر البيئي', 10, 'DAYS', 2, 0, 5),
(236, 'DATA AND INFORMATION ANALYSIS STRATEGY', 'استراتيجيات تحليل البيانات والمعلومات', 5, 'DAYS', 1, 0, 2),
(237, 'CATHODIC PROTECTION FOR GAS PIPELINES', 'الحماية الكاثودية لخطوط الغاز', 5, 'DAYS', 1, 0, 9),
(238, 'MODERN TECHNOLOGIES FOR SEAWATER TREATMENT & DESALINATION PLANTS', 'التقنيات الحديثة لمعالجة مياه البحر ومحطات التحلية', 10, 'DAYS', 2, 0, 3),
(239, 'GLOBAL SPECIFICATIONS, ELECTRICAL TESTING, & CONTROL PROGRAMS', 'المواصفات العالمية والاختبارات الكهربائية وبرامج التحكم', 10, 'DAYS', 2, 0, 3),
(240, 'TREATMENT OF CRACKED CONCRETE & ENGINEERING DRAWING SOFTWARE', 'علاجات الخرسانات المتصدعة وبرامج الرسم الهندسي', 10, 'DAYS', 2, 0, 3),
(241, 'PLANNING AND SOFTWARE UTILIZATION IN ITEM MOVEMENT, MATERIAL SCHEDULING, & INVENTORY CONTROL', 'التخطيط واستخدام البرمجيات في إدارة الأصناف وجدولة المواد ومراقبة المخزون', 10, 'DAYS', 2, 0, 5),
(242, 'MECHANICAL ENGINEERING & PIPELINE DESIGN', 'تصاميم الهندسة الميكانيكية وخطوط الأنابيب', 10, 'DAYS', 2, 0, 3),
(243, 'MODERN COMMUNICATION TECHNOLOGY', 'تكنولوجيا الاتصالات الحديثة', 5, 'DAYS', 1, 0, 2),
(244, 'CONTENT AND LANGUAGE INTEGRATED LEARNING (CLIL)', 'التعلم المتكامل للمحتوى واللغة (CLIL)', 10, 'DAYS', 2, 0, 5),
(245, 'SUPERVISORY SKILLS DEVELOPMENT', 'تطوير المهارات الاشرافية', 10, 'DAYS', 2, 0, 5),
(246, 'MICROSOFT POWER BI', 'ميكروسوفت باور بوينت', 10, 'DAYS', 2, 0, 2),
(247, 'MICROSOFT OUTLOOK EMAIL MANAGEMENT', 'إدارة البريد الالكتروني باستخدام ميكروسوفت اوتلوك', 10, 'DAYS', 2, 0, 2),
(249, 'FIRST AID ', 'الاسعافات الاولية', 5, 'DAYS', 1, 0, 8),
(250, 'OTHER SERVICES', 'خدمات أخرى', 5, 'DAYS', 1, 0, 5),
(251, 'OCCUPATIONAL HEALTH AND SAFETY', 'الصحة والسلامة المهنية', 10, 'DAYS', 2, 0, 6),
(252, 'LEADERSHIP DEVELOPMENT', 'تطوير القيادات', 10, 'DAYS', 2, 0, 1),
(254, 'LINKING TRAINING TO ORGANIZATIONAL GOALS', 'ربط التدريب بأهداف المؤسسة', 5, 'DAYS', 1, 0, 5),
(255, 'PRECISION INSTRUMENT MAINTENANCE', 'صيانة الاجهزة الدقيقة', 10, 'DAYS', 2, 0, 5),
(256, 'MECHANICAL MAINTENANCE OBJECTIVES AND STRATEGIES', 'أهداف واستراتيجيات الصيانة الميكانيكية', 10, 'DAYS', 2, 0, 5),
(257, 'WORKSHOP IN PLANNING FOR PRODUCTION DEVELOPMENT', 'ورشة عمل في التخطيط لتطوير الإنتاج', 5, 'DAYS', 1, 0, 5),
(258, 'MICROSOFT SHAREPOINT', 'ميكروسوف شيربوينت', 10, 'DAYS', 2, 0, 2),
(260, 'INSTRUMENTATION MAINTENANCE', 'صيانة الالات الدقيقة', 10, 'DAYS', 2, 0, 5),
(261, 'PROCUREMENT AND SUPPLY CHAIN MANAGEMENT', 'إدارة المشتريات وسلاسل التوريد', 10, 'DAYS', 2, 0, 1),
(262, 'ADVANCED NEGOTIATION SKILLS', 'مهارات التفاوض المتقدمة', 10, 'DAYS', 2, 0, 5),
(263, 'CONSTRUCTION MANAGEMENT FOR OIL AND GAS PROFESSIONALS', 'ورشة عمل إدارة الانشاءات للعاملين في النفط والغاز', 5, 'DAYS', 1, 0, 3),
(264, 'SAFE HANDLING OF NEW REFRIGERANT GASES', 'التعامل الأمن مع غازات التبريد الحديثة', 10, 'DAYS', 2, 0, 9),
(265, '-----------------', '------------------', 0, 'NONE', 0, 0, 0),
(266, 'SUPPORT SERVICES & TRANSPORTATION MANAGEMENT IN INDUSTRIAL AREAS', 'خدمات الدعم وإدارة النقل في المناطق الصناعية', 10, 'DAYS', 2, 0, 1),
(267, 'CAMERA MAINTENANCE ', 'صيانة الكاميرات', 10, 'DAYS', 2, 0, 5),
(268, 'CHEMICAL ANALYSIS – CORROSION STUDY ', 'التحليل الكيميائي - دراسة التآكل', 10, 'DAYS', 2, 0, 5),
(269, 'CHEMICAL ANALYSIS & PROCESSING ', 'التحليل والمعالجة الكيميائية', 10, 'DAYS', 2, 0, 5),
(270, 'CHEMICAL TREATMENT ', 'المعالجة الكيميائية', 10, 'DAYS', 2, 0, 5),
(271, 'COMMUNICATION SKILLS & PUBLIC RELATIONS ', 'مهارات التواصل والعلاقات العامة', 10, 'DAYS', 2, 0, 5),
(272, 'CORROSION INHIBITORS', 'مثبطات التأكل', 10, 'DAYS', 2, 0, 5),
(273, 'DIESEL ENGINE MAINTENANCE AND OPERATION ', 'صيانة وتشغيل محركات الديزل', 10, 'DAYS', 2, 0, 5),
(274, 'DRILLING FLUIDS ENGINEERING ', 'هندسة سوائل الحفر', 10, 'DAYS', 2, 0, 3),
(275, 'FINANCIAL REPORT WRITING ', 'كتابة التقارير المالية', 10, 'DAYS', 2, 0, 5),
(276, 'FISHING OPERATIONS ', 'عمليات الصيد في صناعة النفط والغاز', 10, 'DAYS', 2, 0, 5),
(278, 'HYDRAULIC CONTROL FOR EQUIPMENT ', 'التحكم الهيدروليكي في المعدات', 10, 'DAYS', 2, 0, 5),
(279, 'INTERNATIONAL ARBITRATION ', 'التحكيم الدولي', 10, 'DAYS', 2, 0, 5),
(280, 'LUBRICATION & GREASING TECHNOLOGY ', 'تقنيات التزييت والتشحيم', 10, 'DAYS', 2, 0, 5),
(282, 'MAINTENANCE AND OPERATIONS ', 'الصيانة والتشغيل', 10, 'DAYS', 2, 0, 1),
(285, 'MODERN MAINTENANCE STRATEGIES ', 'استراتيجيات الصيانة الحديثة', 10, 'DAYS', 2, 0, 5),
(286, 'MODERN TECHNIQUES IN CARPENTRY ', 'التقنيات الحديثة في اعمال النجارة', 10, 'DAYS', 2, 0, 5),
(287, 'MODERN TECHNIQUES IN PLUMBING ', 'التقنيات الحديثة في اعمال السباكة', 10, 'DAYS', 2, 0, 5),
(288, 'OCCUPATIONAL HEALTH & SAFETY ', 'الصحة والسلامة المهنية', 10, 'DAYS', 2, 0, 6),
(289, 'OPERATION AND MAINTENANCE OF GEOLOGICAL LABORATORIES ', 'العمليات والصيانة في المختبرات الجيولوجية', 10, 'DAYS', 2, 0, 5),
(290, 'PLANNING & MAINTENANCE ', 'التخطيط والصيانة', 10, 'DAYS', 2, 0, 5),
(291, 'PLANT OPERATIONS ', 'عمليات التشغيل', 10, 'DAYS', 2, 0, 5),
(292, 'PROCUREMENT & INVENTORY MANAGEMENT ', 'المشتريات وادارة المخزون', 10, 'DAYS', 2, 0, 1),
(293, 'PRODUCTION MAINTENANCE PROGRAMMING ', 'برمجة صيانة الانتاج', 10, 'DAYS', 2, 0, 5),
(294, 'PRODUCTION TESTING ', 'اختبارات الانتاج', 10, 'DAYS', 2, 0, 5),
(295, 'REWRITING REPORTS IN ENGLISH ', 'اعادة كتابة التقارير بالانجليزية', 10, 'DAYS', 2, 0, 5),
(296, 'ROTATING EQUIPMENT ', 'الالات الدوارة', 10, 'DAYS', 2, 0, 5),
(297, 'THE INTEGRATED SYSTEM FOR SECRETARIAT WORK ', 'النظام المدمج لاعمال السكريتيريا', 10, 'DAYS', 2, 0, 5),
(298, 'WELDING OF OIL EQUIPMENT & PIPE INSPECTION ', 'لحام المعدات وفحص خطوط الانابيب', 10, 'DAYS', 2, 0, 5),
(299, 'WELDING TECHNOLOGY ', 'تكنولوجيا اللحام', 10, 'DAYS', 2, 0, 5),
(300, 'WELL TESTING AND SLICKLINE OPERATIONS ', 'اختبارات الابار وعمليات سليكلاين', 10, 'DAYS', 2, 0, 5),
(301, 'ADVANCED ADMINISTRATION SKILLS', 'المهارات الادارية المتقدمة', 20, 'DAYS', 4, 1, 1),
(302, 'ADVANCED PRODUCTION MANAGEMENT', 'الادارة المتقدمة للانتاج', 20, 'DAYS', 4, 1, 1),
(304, 'INDUSTRIAL  SECURITY', 'الامن الصناعي', 20, 'DAYS', 4, 1, 5),
(305, 'LEADERSHIP, STRATEGIC PLANNING, AND EXECUTIVE MANAGEMENT', 'القيادة، والتخطيط الاستراتيجي، و الادارة التنفيذية', 20, 'DAYS', 4, 1, 1),
(306, 'GEOPHYSICAL TECHNICAL TEAM BUILDING', 'بناء الفريق التقني الجيوفيزيائي', 20, 'DAYS', 4, 1, 5),
(307, 'LAB TECHNICAL TEAM BUILDING', 'بناء فريق تقني للمعامل', 20, 'DAYS', 4, 1, 5),
(308, 'STRATEGIC PLANNING FOR SENIOR MANAGEMENT ', 'التخطيط الاستراتيجي للادارة العليا', 10, 'DAYS', 2, 0, 1),
(309, 'HUMAN RESOURCES, STAFFING, AND POLICIES IN THE OIL & GAS SECTOR', 'الملكات و سياسات الموارد البشرية في قطاع النفط', 5, 'DAYS', 1, 0, 5),
(310, 'HUMAN RESOURCES, STAFFING, & POLICIES FOR THE NOC', 'الملكات و سياسات الموارد البشرية', 5, 'DAYS', 1, 0, 5),
(312, 'HUMAN RESOURCES STAFFING, & POLICIES FOR THE NOC', 'الملكات و سياسات الموارد البشرية', 10, 'DAYS', 2, 0, 5),
(313, 'MANAGEMENT OF CRUDE OIL', 'إدارة النفط الخام', 10, 'DAYS', 2, 0, 1),
(314, 'PUBLIC RELATIONS', 'العلاقات العامة', 10, 'DAYS', 2, 0, 5),
(315, 'FUNDAMENTALS OF VOIP COMMUNICATION', 'أساسيات الاتصال بتقنيات الصوت عبر الانترنت', 5, 'DAYS', 1, 0, 2),
(316, 'VARIOUS TRAINING PROGRAMS', 'برامج تدريبية متنوعة', 10, 'DAYS', 2, 0, 5),
(317, 'PRINCIPLES OF PROJECT MANAGEMENT IN OILFIELDS', 'اساسيات ادارة المشروعات في المواقع النفطية', 5, 'DAYS', 1, 0, 1),
(318, 'BLS. (BASIC LIFE SUPPORT) HEART SAVER, FIRST AID, CPR, & AED', 'دَعْمُ الحَيَاةِ الأَسَاسِي (BLS)، مُنْقِذ القَلْب، الإِسْعَافَات الأَوَّلِيَّة، الإِنْعَاشُ القَلْبِيّ الرِئَوِيّ، وَجِهَازُ الصَّدْمَةِ الكَهْرَبَائِيّ التَّلْقَائِيّ (AED)', 5, 'DAYS', 1, 0, 5),
(319, 'HELPDESK', 'الدعم الفني', 10, 'DAYS', 2, 0, 2),
(320, 'WRITING TECHNICAL REPORTS IN ENGLISH', 'كتابة التقارير الفنية باللغة الانجليزية', 10, 'DAYS', 2, 0, 5),
(321, 'WELL SERVICES AND OPERATOR', 'خدمات الآبار والمشغِّلين', 5, 'DAYS', 1, 0, 5),
(322, 'GENERAL MECHANICAL CODE, STD AND SPEC', 'الرموز الميكانيكية، والمعايير، والمواصفات العامة', 5, 'DAYS', 1, 0, 5),
(323, 'DIGITAL TRANSFORMATION AND ARTIFICIAL INTELLIGENCE IN THE OIL AND GAS SECTOR', 'التحول الرقمي والذكاء الاصطناعي في قطاع النفط والغاز', 10, 'DAYS', 2, 0, 5),
(324, 'WELDING AND METALLURGY', 'اللحام وعلم الفلزات', 130, 'DAYS', 26, 6, 5),
(325, 'SUPERVISION AND EXCELLENCE SKILLS IN OIL AND GAS ORGANIZATIONS', 'مهارات الإشراف والتميز في المؤسسات النفطية', 10, 'DAYS', 2, 0, 1),
(326, 'OPERATIONAL GEOLOGY WORKSHOP', 'ورشة عمل في العمليات الجيولوجية', 5, 'DAYS', 1, 0, 3),
(327, 'THE LATEST METHODS FOR ASSET INVENTORY (USING BARCODE)', 'الطرق الحديثة لجرد الاصول باستخدام الباركود', 10, 'DAYS', 2, 0, 5),
(328, 'WORKSHOP IN THE LATEST METHODS FOR ASSET INVENTORY (BARCODE)', 'ورشة عمل الطرق الحديثة لجرد الاصول باستخدام البار كود', 5, 'DAYS', 1, 0, 5),
(329, 'USING AI IN MANAGEMENT', 'إستخدام الذكاء الاصطناعي في الادارة', 10, 'DAYS', 2, 0, 1),
(330, 'SECURITY INSPECTION METHODS AND FACILITY PROTECTION', 'طرق التفتيش الامني والمحافظة على المنشاءات', 20, 'DAYS', 4, 1, 6),
(331, 'WRITING REPORTS AND DATA ANALYSIS', 'كتابة التقارير وتحليل البيانات', 20, 'DAYS', 4, 1, 5),
(332, 'FUNDAMENTALS AND TECHNIQUES OF SURVEYING', 'أساسيات وتقنيات المساحة', 10, 'DAYS', 2, 0, 5),
(334, 'MAINTENANCE OF COMMUNICATION SYSTEMS AND NETWORKS', 'صيانة منظومات وشبكات الاتصالات', 20, 'DAYS', 4, 1, 5),
(335, 'PROJECT MANAGEMENT ESSENTIALS', 'أساسيات إدارة المشاريع', 10, 'DAYS', 2, 0, 3),
(336, 'MANAGEMENT AND MONITORING OF ENTRY AND EXIT GATES AT OIL SITES', 'إدارة والمراقبة على بوابات دخول وخروج المواقع النفطية', 20, 'DAYS', 4, 1, 1),
(337, 'SECURITY INSPECTION PROCEDURES AND FACILITY MAINTENANCE PROGRAM', 'برنامج طرق التفتيش الأمني والمحافظة على المنشات', 20, 'DAYS', 4, 1, 5),
(339, 'OPTICAL FIBER ', 'الألياف الضوئية', 10, 'DAYS', 2, 0, 2),
(340, 'Onsite Training Service', 'خدمات تدريب أثناء العمل', 10, 'DAYS', 2, 0, 5),
(341, 'PROJECT MANAGEMENT IN OIL AND GAS SECTOR', 'إدارة المشروعات في قطاع النفط والغاز', 10, 'DAYS', 2, 0, 1),
(342, 'ARTIFICIAL INTELLIGENCE', 'الذكاء الاصطناعي', 10, 'DAYS', 2, 0, 2),
(343, 'API 5L CARBON STEEL PIPELINE: DESIGN, INSPECTION & INTEGRITY MANAGEMENT', 'خط أنابيب الفولاذ الكربوني API 5L: التصميم والتفتيش وإدارة السلامة', 10, 'DAYS', 2, 0, 9),
(344, 'COMPREHENSIVE PROJECT MANAGEMENT TRAINING', 'التدريب الشامل على إدارة المشاريع', 130, 'DAYS', 26, 6, 1),
(345, 'COMPUTER PROGRAMMING', 'برمجة الحاسوب', 10, 'DAYS', 2, 0, 2),
(346, 'PERMIT TO WORK (PTW) SYSTEM IN OIL & GAS INDUSTRY WORKSHOP', 'ورشة عمل نظام تصريح العمل (PTW) في صناعة النفط والغاز', 5, 'DAYS', 1, 0, 9),
(347, 'FIBER OPTIC NETWORK DESIGN SPECIALIST', ' تصميم شبكات الألياف الضوئية', 10, 'DAYS', 2, 0, 5),
(348, 'ADMINISTRATIVE SKILLS DEVELOPMENT PROGRAM', 'برنامج تنمية المهارات الإدارية', 65, 'DAYS', 13, 3, 5),
(349, 'PETROLEUM ECONOMICS', 'اقتصاديات البترول', 5, 'DAYS', 1, 0, 4),
(350, 'Petroleum Project Economics & Risk Analysis', ' اقتصاديات مشروعات البترول وتحليل المخاطر', 5, 'DAYS', 1, 0, 1),
(351, 'BASIC PETROLEUM TECHNOLOGY', ' أساسيات تكنولوجيا البترول', 5, 'DAYS', 1, 0, 3),
(352, 'Introduction to Oil & Gas Industry', 'مقدمة في صناعة النفط والغاز', 5, 'DAYS', 1, 0, 3),
(353, 'Oil & Gas Production Operation', 'عمليات إنتاج النفط والغاز', 5, 'DAYS', 1, 0, 3),
(354, 'ISO 9001:2015 QUALITY MANAGEMENT SYSTEM (QMS)', 'نظام إدارة الجودة', 5, 'DAYS', 1, NULL, 5),
(355, 'MODERN MANAGEMENT PROGRAM', 'برنامج الإدارة الحديثة', 10, 'DAYS', 2, NULL, 1),
(356, 'MS DEFENDER XDR AND SENTINEL', 'ميكروسوفت DEFENDER XDR  and SENTINEL', 10, 'DAYS', 2, NULL, 2),
(357, 'WORKSHOP: FEASIBILITY STUDY REPORTS, COST ACCOUNTING & MARKETING RESEARCH', 'ورشة عمل: تقارير دراسة الجدوى الاقتصادية ومحاسبة التكاليف و البحوث التسويقية', 4, 'DAYS', NULL, NULL, 4),
(358, 'Artificial Intelligence for Executives and Consultants', 'الذكاء الاصطناعي للمدراء والاستشاريين', 4, 'DAYS', NULL, NULL, 5),
(359, 'AUTODESK CIVIL 3D', 'اوتوكاد المدني ثلاثي الابعاد', 10, 'DAYS', 2, NULL, 5),
(360, 'Project Management Professional Program', 'برنامج محترف ادارة المشروعات', 130, 'DAYS', 26, NULL, 1),
(361, 'Project Management Foundation', 'أساسيات ادارة المشروعات', 65, 'DAYS', 13, NULL, 1),
(362, 'Understanding Artificial Intelligence - AI Awareness and Practical Knowledge', 'فهم الذكاء الاصطناعي - الوعي بالذكاء الاصطناعي والمعرفة العملية', 10, 'DAYS', 2, NULL, 5),
(363, 'HUMAN RESOURCES PROFESSIONAL', 'محترف الموارد البشرية', 130, 'DAYS', 26, NULL, 1),
(364, 'Natural Gas Transportation Operations Management', 'إدارة عمليات نقل الغاز الطبيعي', 5, 'DAYS', NULL, NULL, 3),
(365, 'Maintenance Operations Management', 'إدارة عمليات الصيانة', 5, 'DAYS', NULL, NULL, 3),
(366, 'Managerial Skills Development', 'تنمية المهارات الإدارية', 5, 'DAYS', NULL, NULL, 1),
(367, 'Gas Turbine Operation', 'تشغيل التوربينات الغازية', 5, 'DAYS', NULL, NULL, 3),
(368, 'LOGISTICS SERVICES AT WELL DRILLING SITES', 'الخدمات اللوجستية في مواقع حفر الابار النفطية', 10, 'DAYS', 2, NULL, 9),
(369, 'PROCUREMENT MANAGEMENT', 'ادارة المشتريات', 10, 'DAYS', 2, NULL, 5),
(370, 'ORGANIZING PROCEDURES & ADMINISTRATIVE WORK METHOD IMPROVEMENT', 'تنظيم الإجراءات وتطوير أساليب العمل الإداري', 5, 'DAYS', 1, NULL, 5),
(371, 'MODERN MAINTENANCE MANAGEMENT FOR POWER PLANTS AND ELECTRICAL TRANSMISSION NETWORKS', 'إدارة الصيانة الحديثة في محطات توليد الطاقة وشبكات النقل الكهربائي', 5, 'DAYS', 1, NULL, 3),
(372, 'HND ELECTRICAL ENGINEERING', 'HND في الهندسة الكهربائية', 260, 'DAYS', 52, 12, 3),
(373, 'Modern Technologies for the Protection of Oil Facilities', 'التقنيات الحديثة لحماية المنشآت النفطية', 5, 'DAYS', 1, NULL, 6),
(374, 'Aligning Training Programs with Organizational Goals', 'مواءمة البرامج التدريبية مع الأهداف التنظيمية', 5, 'DAYS', 1, NULL, 5),
(375, 'EXCELLENCE IN SERVICES & IMPROVEMENT ORGANIZATIONAL EFFICIENCY', 'التميز في الخدمات وتحسين الكفاءة المؤسسية', 5, 'DAYS', 1, NULL, 5),
(376, 'ALIGNMENT & VIBRATION ANALYSIS FOR OIL & GAS EQUIPMENT', 'تحليل المحاذاة والاهتزاز لمعدات النفط والغاز', 130, 'DAYS', NULL, NULL, 3),
(377, 'ARTIFICIAL INTELLIGENCE (AI) APPLICATIONS IN ADMINISTRATIVE TASKS', 'استخدام الذكاء الاصطناعي في المهام المكتبية', 3, 'DAYS', NULL, NULL, 5),
(379, 'PROFESSIONAL LEGAL TRANSLATION', 'الترجمة القانونية الاحترافية', 3, 'DAYS', NULL, NULL, 5),
(380, 'ADVANCED STRATEGIC MANAGEMENT SKILLS', 'مهارات الادارة الاستراتيجية المتقدمة', 5, 'DAYS', NULL, NULL, 1),
(381, 'MODERN & CONTEMPORARY TRENDS IN STRATEGIC POLICIES OF THE PERSONNEL AFFAIRS COMMITTEE', 'الاتجاهات الحديثة والمعاصرة للسياسات الاستراتيجية للجنة شؤون الموظفين', 5, 'DAYS', NULL, NULL, 1),
(382, 'Administrative Leadership', 'القيادة الادارية', 5, 'DAYS', NULL, NULL, 1),
(383, 'GAS PIPELINES CATHODE PROTECTION ', 'الحماية الكاثودية لخط الغاز', 4, 'DAYS', NULL, NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `cust_id` int(11) NOT NULL,
  `cust_name` varchar(255) NOT NULL,
  `cust_code` varchar(255) DEFAULT NULL,
  `cust_address` varchar(1000) DEFAULT NULL,
  `cust_telephone` varchar(255) DEFAULT NULL,
  `cust_contact` varchar(255) DEFAULT NULL,
  `cust_mobile` varchar(255) DEFAULT NULL,
  `cust_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `inst_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `qual_id` int(11) NOT NULL,
  `major` varchar(255) DEFAULT NULL,
  `interests` varchar(1000) DEFAULT NULL,
  `keywords` varchar(1000) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `social` varchar(255) DEFAULT NULL,
  `mobile` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `nation_id` int(11) DEFAULT NULL,
  `count_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `inst_portrait` varchar(255) NOT NULL DEFAULT 'photo_uploads/instructor_male.jpg',
  `contract_file` varchar(255) DEFAULT NULL,
  `bank_details` varchar(500) DEFAULT 'No details provided',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `gender` enum('male','female','other') DEFAULT 'male',
  `is_verified` tinyint(1) DEFAULT 0,
  `profile_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `arabic_level` enum('None','Beginner','Intermediate','Advanced') NOT NULL,
  `english_level` enum('None','Beginner','Intermediate','Advanced') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructors_courses`
--

CREATE TABLE `instructors_courses` (
  `inst_course_id` int(11) NOT NULL,
  `inst_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructors_email`
--

CREATE TABLE `instructors_email` (
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mobile` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructor_dues`
--

CREATE TABLE `instructor_dues` (
  `due_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `course_date` date NOT NULL,
  `num_participants` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `due_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructor_due_instances`
--

CREATE TABLE `instructor_due_instances` (
  `id` int(11) NOT NULL,
  `due_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `quot_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `inv_id` int(11) NOT NULL,
  `quot_id` int(11) NOT NULL,
  `quot_instance_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `cost` decimal(19,0) NOT NULL,
  `trainees` int(11) NOT NULL,
  `inv_date` date NOT NULL,
  `ven_id` int(11) DEFAULT NULL,
  `inv_file` varchar(255) DEFAULT NULL,
  `total` decimal(10,0) NOT NULL,
  `status` enum('Pending','In_process','Paid','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nations`
--

CREATE TABLE `nations` (
  `nation_id` int(11) NOT NULL,
  `nation_name` varchar(255) NOT NULL DEFAULT 'Egyptian'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `nations`
--

INSERT INTO `nations` (`nation_id`, `nation_name`) VALUES
(1, 'LIBYAN'),
(2, 'SYRIAN'),
(3, 'TURKISH'),
(4, 'EGYPTIAN'),
(5, 'IRAQI'),
(6, 'JORDANIAN'),
(7, 'LEBANON'),
(8, 'IRANI'),
(9, 'OTHER'),
(10, 'PALESTINIAN'),
(11, 'SUDANESE');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `ord_id` int(11) NOT NULL,
  `ord_type_id` int(11) DEFAULT NULL,
  `cust_id` int(11) NOT NULL,
  `ord_subject` varchar(255) NOT NULL,
  `ord_date` date NOT NULL,
  `ord_details` varchar(5000) DEFAULT NULL,
  `ord_file` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ord_types`
--

CREATE TABLE `ord_types` (
  `ord_type_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ord_types`
--

INSERT INTO `ord_types` (`ord_type_id`, `type_name`) VALUES
(1, 'QUOTATION'),
(2, 'LOGISTICS'),
(3, 'TRAINING INFORMATION'),
(4, 'ENQUIRY'),
(5, 'CONSULTING'),
(6, 'OTHER SERVICES'),
(7, 'INTERNAL WORK');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `due_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `status` enum('Paid','Partial','Pending') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qualifications`
--

CREATE TABLE `qualifications` (
  `qual_id` int(11) NOT NULL,
  `qual_title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qualifications`
--

INSERT INTO `qualifications` (`qual_id`, `qual_title`) VALUES
(1, 'PhD'),
(2, 'MASTERS'),
(3, 'BACHLER'),
(4, 'HIGH SCHOOL'),
(5, 'HIGHER DIPLOMA'),
(6, 'SOME CERTIFICATES');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `quot_id` int(11) NOT NULL,
  `quot_ref` varchar(255) NOT NULL,
  `ord_id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `trainees` int(11) DEFAULT NULL,
  `duration` varchar(255) NOT NULL DEFAULT '10 DAYS',
  `cost` decimal(10,2) DEFAULT NULL,
  `quot_date` date NOT NULL,
  `ven_id` int(11) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `introduction` text DEFAULT NULL,
  `objectives` text DEFAULT NULL,
  `audiences` text DEFAULT NULL,
  `outlines` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_instances`
--

CREATE TABLE `quotation_instances` (
  `instance_id` int(11) NOT NULL,
  `quot_id` int(11) DEFAULT NULL,
  `cust_id` int(255) NOT NULL,
  `instance_ref` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `ven_id` int(11) DEFAULT NULL,
  `inv_id` int(11) DEFAULT NULL,
  `attendance_sheet` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_instructors`
--

CREATE TABLE `quotation_instructors` (
  `id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_participants`
--

CREATE TABLE `quotation_participants` (
  `part_id` int(11) NOT NULL,
  `instance_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `full_name_a` varchar(255) DEFAULT NULL,
  `payroll_no` varchar(100) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `cust_id` int(11) NOT NULL,
  `mobile` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `email`, `user_password`, `user_role`, `full_name`, `avatar`) VALUES
(1, 'admin', 'admin@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'System Administrator', 'uploads/avatars/avatar_1_1754764200.PNG'),
(3, 'accountant', 'account@avenueinternational.net', '$2y$10$/rxi2aTHn5fNhqIfUEGH3eXZizyshx8wDukm9HENC28XLLg7hh0Pq', 'ACCOUNTANT', 'Company Accountant', 'uploads/avatars/avatar_3.jpg'),
(6, 'abdullah', 'cto@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'Abdullah Ben Amer', 'uploads/avatars/avatar_6.jpg'),
(25, 'bushra', 'secretary@avenueinternational.net', '$2y$10$BbXjQKTjCrTmvUEx83.7pe6hvmSi0IuGiR6purpTISaKE0btE.R.u', 'ADMIN', 'BUSHRA ADNAN', 'uploads/avatars/avatar_25_1754815732.PNG'),
(26, 'omar', 'administration@avenueinternational.net', '$2y$10$MflNMIbi2skXYC6JTNdc6.DehEuYvTiBz4J5wGlr2GmMV0mulFsyC', 'USER', 'Omar Khalid', 'uploads/avatars/avatar_26_1754772044.png'),
(29, 'ammar', 'ammar@harvest.com', '$2y$10$eeBUXKajx8eTDPvIIMSRG.TfXvLLqQ5Ym9.UuJPkxNWLZLQzufEIe', 'GUEST', 'AMMAR ABO SOFIAN', 'uploads/avatars/avatar_29.png'),
(30, 'sys', 'sys@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'SYSTEM ADMIN', 'uploads/avatars/default_male.png'),
(31, 'user', 'user@avenueinternational.net', '$2y$10$ftxt0dVslStTm/mj2ZO9beT3la6aSpqqbuuxLiReqICTrJFGvUV9O', 'USER', 'NORMAL USER', 'uploads/avatars/1754021642_goals333.jpg'),
(1, 'admin', 'admin@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'System Administrator', 'uploads/avatars/avatar_1_1754764200.PNG'),
(3, 'accountant', 'account@avenueinternational.net', '$2y$10$/rxi2aTHn5fNhqIfUEGH3eXZizyshx8wDukm9HENC28XLLg7hh0Pq', 'ACCOUNTANT', 'Company Accountant', 'uploads/avatars/avatar_3.jpg'),
(6, 'abdullah', 'cto@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'Abdullah Ben Amer', 'uploads/avatars/avatar_6.jpg'),
(25, 'bushra', 'secretary@avenueinternational.net', '$2y$10$BbXjQKTjCrTmvUEx83.7pe6hvmSi0IuGiR6purpTISaKE0btE.R.u', 'ADMIN', 'BUSHRA ADNAN', 'uploads/avatars/avatar_25_1754815732.PNG'),
(26, 'omar', 'administration@avenueinternational.net', '$2y$10$MflNMIbi2skXYC6JTNdc6.DehEuYvTiBz4J5wGlr2GmMV0mulFsyC', 'USER', 'Omar Khalid', 'uploads/avatars/avatar_26_1754772044.png'),
(29, 'ammar', 'ammar@harvest.com', '$2y$10$eeBUXKajx8eTDPvIIMSRG.TfXvLLqQ5Ym9.UuJPkxNWLZLQzufEIe', 'GUEST', 'AMMAR ABO SOFIAN', 'uploads/avatars/avatar_29.png'),
(30, 'sys', 'sys@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'SYSTEM ADMIN', 'uploads/avatars/default_male.png'),
(31, 'user', 'user@avenueinternational.net', '$2y$10$ftxt0dVslStTm/mj2ZO9beT3la6aSpqqbuuxLiReqICTrJFGvUV9O', 'USER', 'NORMAL USER', 'uploads/avatars/1754021642_goals333.jpg'),
(1, 'admin', 'admin@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'System Administrator', 'uploads/avatars/avatar_1_1754764200.PNG'),
(3, 'accountant', 'account@avenueinternational.net', '$2y$10$/rxi2aTHn5fNhqIfUEGH3eXZizyshx8wDukm9HENC28XLLg7hh0Pq', 'ACCOUNTANT', 'Company Accountant', 'uploads/avatars/avatar_3.jpg'),
(6, 'abdullah', 'cto@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'Abdullah Ben Amer', 'uploads/avatars/avatar_6.jpg'),
(25, 'bushra', 'secretary@avenueinternational.net', '$2y$10$BbXjQKTjCrTmvUEx83.7pe6hvmSi0IuGiR6purpTISaKE0btE.R.u', 'ADMIN', 'BUSHRA ADNAN', 'uploads/avatars/avatar_25_1754815732.PNG'),
(26, 'omar', 'administration@avenueinternational.net', '$2y$10$MflNMIbi2skXYC6JTNdc6.DehEuYvTiBz4J5wGlr2GmMV0mulFsyC', 'USER', 'Omar Khalid', 'uploads/avatars/avatar_26_1754772044.png'),
(29, 'ammar', 'ammar@harvest.com', '$2y$10$eeBUXKajx8eTDPvIIMSRG.TfXvLLqQ5Ym9.UuJPkxNWLZLQzufEIe', 'GUEST', 'AMMAR ABO SOFIAN', 'uploads/avatars/avatar_29.png'),
(30, 'sys', 'sys@avenueinternational.net', '$2y$10$SXnTfANvP2XdGJpn2hiZI.0W2I9AhAOoIsvx/GEOpGDXFh6SaIJFu', 'ADMIN', 'SYSTEM ADMIN', 'uploads/avatars/default_male.png'),
(31, 'user', 'user@avenueinternational.net', '$2y$10$ftxt0dVslStTm/mj2ZO9beT3la6aSpqqbuuxLiReqICTrJFGvUV9O', 'USER', 'NORMAL USER', 'uploads/avatars/1754021642_goals333.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `ven_id` int(11) NOT NULL,
  `ven_name` varchar(255) NOT NULL,
  `ven_address` varchar(1000) DEFAULT NULL,
  `count_id` int(11) NOT NULL,
  `city_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`ven_id`, `ven_name`, `ven_address`, `count_id`, `city_id`) VALUES
(1, 'ISTANBUL - TURKEY', 'Cumhuriyet Mah. 10 Ergenekon Cad., Ahmetbey plaza K4, Pangalti, Şişli 34360', 1, 1),
(2, 'AVENUE INTERNATIONAL - UAE', 'DUBAI, UAE', 5, 4),
(3, 'AVENUE INTERNATIONAL - OMAN', 'SALALAH, OMAN EMIRATE', 4, 5),
(4, 'AVENUE INTERNATIONAL - TUNISIA', 'TUNIS, TUNISIA - 7th floor, BH Leasing, Centre Urbain Nord, 1082', 8, 13),
(5, 'AVENUE INTERNATIONAL - EYGEPT', 'CAIRO, EGYPT - 4A Juhaiyna street Dokki, Giza', 2, 7),
(6, 'RAMADA HOTEL', 'ISTANBUL, ŞIŞLI - OSMANBEY', 1, 1),
(7, 'ARTS HOTEL', 'ISTANBUL, ŞIŞLI - HARBIYE', 1, 1),
(8, 'LUTOS HALLS', 'ISTANBUL, ŞIŞLI - OSMANBEY', 1, 1),
(1, 'ISTANBUL - TURKEY', 'Cumhuriyet Mah. 10 Ergenekon Cad., Ahmetbey plaza K4, Pangalti, Şişli 34360', 1, 1),
(2, 'AVENUE INTERNATIONAL - UAE', 'DUBAI, UAE', 5, 4),
(3, 'AVENUE INTERNATIONAL - OMAN', 'SALALAH, OMAN EMIRATE', 4, 5),
(4, 'AVENUE INTERNATIONAL - TUNISIA', 'TUNIS, TUNISIA - 7th floor, BH Leasing, Centre Urbain Nord, 1082', 8, 13),
(5, 'AVENUE INTERNATIONAL - EYGEPT', 'CAIRO, EGYPT - 4A Juhaiyna street Dokki, Giza', 2, 7),
(6, 'RAMADA HOTEL', 'ISTANBUL, ŞIŞLI - OSMANBEY', 1, 1),
(7, 'ARTS HOTEL', 'ISTANBUL, ŞIŞLI - HARBIYE', 1, 1),
(8, 'LUTOS HALLS', 'ISTANBUL, ŞIŞLI - OSMANBEY', 1, 1),
(1, 'ISTANBUL - TURKEY', 'Cumhuriyet Mah. 10 Ergenekon Cad., Ahmetbey plaza K4, Pangalti, Şişli 34360', 1, 1),
(2, 'AVENUE INTERNATIONAL - UAE', 'DUBAI, UAE', 5, 4),
(3, 'AVENUE INTERNATIONAL - OMAN', 'SALALAH, OMAN EMIRATE', 4, 5),
(4, 'AVENUE INTERNATIONAL - TUNISIA', 'TUNIS, TUNISIA - 7th floor, BH Leasing, Centre Urbain Nord, 1082', 8, 13),
(5, 'AVENUE INTERNATIONAL - EYGEPT', 'CAIRO, EGYPT - 4A Juhaiyna street Dokki, Giza', 2, 7),
(6, 'RAMADA HOTEL', 'ISTANBUL, ŞIŞLI - OSMANBEY', 1, 1),
(7, 'ARTS HOTEL', 'ISTANBUL, ŞIŞLI - HARBIYE', 1, 1),
(8, 'LUTOS HALLS', 'ISTANBUL, ŞIŞLI - OSMANBEY', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `cat_code` (`cat_code`),
  ADD UNIQUE KEY `cat_name` (`cat_name`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`cert_id`),
  ADD KEY `FK_certificates_courses_course_id` (`course_id`),
  ADD KEY `FK_certificates_customers_cust_id` (`cust_id`),
  ADD KEY `fk_cert_venues` (`ven_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `FK_cities_countries_count_id` (`count_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`count_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_title` (`course_title`),
  ADD KEY `FK_courses_categories_cat_id` (`cat_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`cust_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`inst_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `FK_instructors_countries_count_id` (`count_id`),
  ADD KEY `FK_instructors_city` (`city_id`),
  ADD KEY `FK_instructors_qualifications_qual_id` (`qual_id`),
  ADD KEY `FK_instructor_nation` (`nation_id`);

--
-- Indexes for table `instructors_courses`
--
ALTER TABLE `instructors_courses`
  ADD PRIMARY KEY (`inst_course_id`),
  ADD KEY `inst_id` (`inst_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `instructor_dues`
--
ALTER TABLE `instructor_dues`
  ADD PRIMARY KEY (`due_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `instructor_due_instances`
--
ALTER TABLE `instructor_due_instances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `due_id` (`due_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`inv_id`),
  ADD KEY `quot_id` (`quot_id`),
  ADD KEY `quot_instance_id` (`quot_instance_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `cust_id` (`cust_id`),
  ADD KEY `ven_id` (`ven_id`);

--
-- Indexes for table `nations`
--
ALTER TABLE `nations`
  ADD PRIMARY KEY (`nation_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ord_id`),
  ADD KEY `FK_orders_customers_cust_id` (`cust_id`),
  ADD KEY `FK_orders_ord_types_ord_type_id` (`ord_type_id`),
  ADD KEY `FK_orders_users_user_id` (`user_id`);

--
-- Indexes for table `ord_types`
--
ALTER TABLE `ord_types`
  ADD PRIMARY KEY (`ord_type_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `due_id` (`due_id`);

--
-- Indexes for table `qualifications`
--
ALTER TABLE `qualifications`
  ADD PRIMARY KEY (`qual_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`quot_id`),
  ADD KEY `FK_quotations_courses_course_id` (`course_id`),
  ADD KEY `FK_quotations_customers_cust_id` (`cust_id`),
  ADD KEY `FK_quotations_orders_ord_id` (`ord_id`),
  ADD KEY `FK_quotations_venues_ven_id` (`ven_id`),
  ADD KEY `FK_quotations_category_cat_id` (`cat_id`);

--
-- Indexes for table `quotation_instances`
--
ALTER TABLE `quotation_instances`
  ADD PRIMARY KEY (`instance_id`),
  ADD KEY `quot_id` (`quot_id`),
  ADD KEY `inv_id` (`inv_id`),
  ADD KEY `fk_venue_id` (`ven_id`);

--
-- Indexes for table `quotation_instructors`
--
ALTER TABLE `quotation_instructors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instance_id` (`instance_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `quotation_participants`
--
ALTER TABLE `quotation_participants`
  ADD PRIMARY KEY (`part_id`),
  ADD KEY `instance_id` (`instance_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `cert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1475;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `count_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=384;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `cust_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `inst_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `instructors_courses`
--
ALTER TABLE `instructors_courses`
  MODIFY `inst_course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1461;

--
-- AUTO_INCREMENT for table `instructor_dues`
--
ALTER TABLE `instructor_dues`
  MODIFY `due_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `instructor_due_instances`
--
ALTER TABLE `instructor_due_instances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `inv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=361;

--
-- AUTO_INCREMENT for table `nations`
--
ALTER TABLE `nations`
  MODIFY `nation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `ord_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `ord_types`
--
ALTER TABLE `ord_types`
  MODIFY `ord_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qualifications`
--
ALTER TABLE `qualifications`
  MODIFY `qual_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `quot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2722;

--
-- AUTO_INCREMENT for table `quotation_instances`
--
ALTER TABLE `quotation_instances`
  MODIFY `instance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;

--
-- AUTO_INCREMENT for table `quotation_instructors`
--
ALTER TABLE `quotation_instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=543;

--
-- AUTO_INCREMENT for table `quotation_participants`
--
ALTER TABLE `quotation_participants`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2657;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `instructors`
--
ALTER TABLE `instructors`
  ADD CONSTRAINT `FK_instructor_nation` FOREIGN KEY (`nation_id`) REFERENCES `nations` (`nation_id`);

--
-- Constraints for table `instructors_courses`
--
ALTER TABLE `instructors_courses`
  ADD CONSTRAINT `instructors_courses_ibfk_1` FOREIGN KEY (`inst_id`) REFERENCES `instructors` (`inst_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `instructors_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `instructor_due_instances`
--
ALTER TABLE `instructor_due_instances`
  ADD CONSTRAINT `instructor_due_instances_ibfk_1` FOREIGN KEY (`due_id`) REFERENCES `instructor_dues` (`due_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
