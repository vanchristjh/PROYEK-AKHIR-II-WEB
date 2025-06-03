/**
 * Dummy Statistics Data
 * This file contains sample data for testing the assignment statistics views
 */

window.dummyStatisticsData = {
    // Sample assignment information
    assignment: {
        title: "Tugas Matematika - Kalkulus Integral",
        subject: "Matematika",
        classes: ["XI IPA 1", "XI IPA 2", "XI IPA 3"],
        deadline: "2023-06-30 23:59",
        isExpired: true
    },

    // Summary statistics
    summary: {
        totalStudents: 92,
        submissionCount: 78,
        submissionRate: 84.78,
        pendingCount: 15,
        gradedCount: 63,
        gradeRate: 80.77,
        averageScore: 75.8
    },

    // Score distribution
    scoreDistribution: {
        a: 25, // 80-100
        b: 30, // 70-79
        c: 10, // 60-69
        d: 5,  // 50-59
        e: 8   // 0-49
    },

    // Submission timeline
    submissionDates: {
        "2023-06-24": 5,
        "2023-06-25": 8,
        "2023-06-26": 12,
        "2023-06-27": 19,
        "2023-06-28": 15,
        "2023-06-29": 10,
        "2023-06-30": 9
    },

    // Class-specific data
    classData: {
        "XI IPA 1": {
            totalStudents: 32,
            submissionCount: 28,
            avgScore: 78.3
        },
        "XI IPA 2": {
            totalStudents: 30,
            submissionCount: 25,
            avgScore: 73.5
        },
        "XI IPA 3": {
            totalStudents: 30,
            submissionCount: 25,
            avgScore: 74.9
        }
    },

    // Generate random submission data for testing
    generateRandomSubmissions: function(count) {
        const submissions = [];
        const names = [
            "Aditya Pratama", "Budi Santoso", "Citra Dewi", "Dian Kusuma", 
            "Eko Saputro", "Fitriani Sari", "Gita Nuraini", "Hadi Wijaya",
            "Indah Permata", "Joko Susanto", "Kartika Sari", "Laila Nur",
            "Mamat Santoso", "Nur Hidayah", "Oktavia Putri", "Putra Wijaya",
            "Qori Amalia", "Rini Anggraini", "Surya Darma", "Tia Lestari"
        ];
        const classOptions = ["XI IPA 1", "XI IPA 2", "XI IPA 3"];
        
        for (let i = 0; i < count; i++) {
            const randomName = names[Math.floor(Math.random() * names.length)];
            const randomClass = classOptions[Math.floor(Math.random() * classOptions.length)];
            const randomScore = Math.floor(Math.random() * 100) + 1;
            const randomSubmissionDate = this.randomDate(new Date('2023-06-24'), new Date('2023-06-30'));
            
            submissions.push({
                id: i + 1,
                student_name: randomName,
                class: randomClass,
                score: randomScore,
                status: randomScore > 0 ? 'Dinilai' : 'Belum dinilai',
                submission_date: randomSubmissionDate.toISOString().split('T')[0],
                is_late: randomSubmissionDate > new Date('2023-06-30'),
                file: 'submission-' + (i+1) + '.pdf'
            });
        }
        return submissions;
    },
    
    // Helper function to generate random date
    randomDate: function(start, end) {
        return new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
    }
};

// Generate 20 random submissions for demo purposes
window.dummyStatisticsData.submissions = window.dummyStatisticsData.generateRandomSubmissions(20);

// Log that the dummy data has been loaded
console.log('Dummy statistics data loaded for testing');
