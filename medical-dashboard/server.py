from flask import Flask, jsonify
from flask_cors import CORS
import mysql.connector
from datetime import datetime

app = Flask(__name__)
CORS(app)

def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="medical"
    )

@app.route('/api/demographics')
def get_demographics():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        # Get age distribution
        cursor.execute("""
            SELECT 
                CASE
                    WHEN age <= 17 THEN '0-17'
                    WHEN age <= 30 THEN '18-30'
                    WHEN age <= 50 THEN '31-50'
                    WHEN age <= 70 THEN '51-70'
                    ELSE '70+'
                END as group,
                gender,
                COUNT(*) as count
            FROM patient
            GROUP BY 
                CASE
                    WHEN age <= 17 THEN '0-17'
                    WHEN age <= 30 THEN '18-30'
                    WHEN age <= 50 THEN '31-50'
                    WHEN age <= 70 THEN '51-70'
                    ELSE '70+'
                END,
                gender
            ORDER BY group
        """)
        age_data = cursor.fetchall()
        
        # Transform data for the chart
        age_groups = {}
        for row in age_data:
            if row['group'] not in age_groups:
                age_groups[row['group']] = {'group': row['group'], 'male': 0, 'female': 0}
            age_groups[row['group']]['male' if row['gender'] == 'Male' else 'female'] = row['count']
        
        # Get total patients and gender distribution
        cursor.execute("""
            SELECT gender, COUNT(*) as count 
            FROM patient 
            GROUP BY gender
        """)
        gender_data = cursor.fetchall()
        
        return jsonify({
            'ageGroups': list(age_groups.values()),
            'genderDistribution': gender_data,
            'totalPatients': sum(row['count'] for row in gender_data)
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500
    finally:
        cursor.close()
        conn.close()

@app.route('/api/disease-trends')
def get_disease_trends():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        cursor.execute("""
            SELECT 
                DATE_FORMAT(d.diagnosis_date, '%Y-%m') as month,
                dis.disease_name,
                COUNT(*) as count
            FROM diagnosis d
            JOIN diseases dis ON d.suspected_disease_id = dis.disease_id
            GROUP BY month, dis.disease_name
            ORDER BY month, dis.disease_name
        """)
        
        results = cursor.fetchall()
        
        # Transform data for the chart
        trends_data = {}
        for row in results:
            if row['month'] not in trends_data:
                trends_data[row['month']] = {'month': row['month']}
            trends_data[row['month']][row['disease_name']] = row['count']
        
        return jsonify(list(trends_data.values()))
    except Exception as e:
        return jsonify({'error': str(e)}), 500
    finally:
        cursor.close()
        conn.close()

@app.route('/api/hospital-referrals')
def get_hospital_referrals():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        cursor.execute("""
            SELECT 
                h.name as hospital,
                COUNT(*) as referrals
            FROM diagnosis d
            JOIN kenya_specialist_hospitals h ON d.hospital_referral_id = h.hospital_id
            GROUP BY h.hospital_id, h.name
            ORDER BY referrals DESC
        """)
        
        return jsonify(cursor.fetchall())
    except Exception as e:
        return jsonify({'error': str(e)}), 500
    finally:
        cursor.close()
        conn.close()

if __name__ == '__main__':
    app.run(debug=True, port=5000)