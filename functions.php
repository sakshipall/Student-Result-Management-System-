<?php
/**
 * Common helper functions used across the Student Result Management System.
 */

// Labels for the 5 subjects (change here if subject names need to be updated)
$GLOBALS['subject_fields'] = ['subject1', 'subject2', 'subject3', 'subject4', 'subject5'];
$GLOBALS['subject_labels'] = ['Subject 1', 'Subject 2', 'Subject 3', 'Subject 4', 'Subject 5'];

// Decide grade from percentage using the college grading scale
function calculateGrade($percentage)
{
    if ($percentage >= 90) {
        return 'A+';
    } elseif ($percentage >= 80) {
        return 'A';
    } elseif ($percentage >= 70) {
        return 'B+';
    } elseif ($percentage >= 60) {
        return 'B';
    } elseif ($percentage >= 50) {
        return 'C';
    } elseif ($percentage >= 40) {
        return 'D';
    } else {
        return 'F';
    }
}

// A student passes only if every subject has at least 40 marks
function calculateResultStatus($marksArray)
{
    foreach ($marksArray as $mark) {
        if ($mark < 40) {
            return 'FAIL';
        }
    }
    return 'PASS';
}

// Save a one-time flash message in the session
function setFlash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// Read and clear the flash message
function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Escape output safely and trim input
function clean($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}
