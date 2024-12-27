from flask import Flask, jsonify, render_template
import pymysql

app = Flask(__name__)

# Database connection
def get_db_connection():
    return pymysql.connect(
        host="localhost",
        user="root",
        password="",
        database="medical",
        cursorclass=pymysql.cursors.DictCursor
    )

# Helper function to get disease distribution by age
def get_disease_by_age():
    with get_db_connection() as conn:
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT p.age_group, COUNT(d.diagnosis_id) as count
                FROM diagnosis d
                JOIN patient p ON d.patient_id = p.patient_id
                GROUP BY p.age_group
                ORDER BY count DESC
            """)
            return cursor.fetchall()

# Helper function to get disease distribution by religion
def get_disease_by_religion():
    with get_db_connection() as conn:
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT p.religion, COUNT(d.diagnosis_id) as count
                FROM diagnosis d
                JOIN patient p ON d.patient_id = p.patient_id
                GROUP BY p.religion
                ORDER BY count DESC
            """)
            return cursor.fetchall()

# Helper function to get disease distribution by hospital
def get_disease_by_hospital():
    with get_db_connection() as conn:
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT h.name, COUNT(d.diagnosis_id) as count
                FROM diagnosis d
                JOIN kenya_specialist_hospitals h ON d.hospital_referral_id = h.hospital_id
                GROUP BY h.hospital_id
                ORDER BY count DESC
            """)
            return cursor.fetchall()

# Helper function to get disease distribution by referrals
def get_disease_by_referrals():
    with get_db_connection() as conn:
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT h.name, COUNT(d.diagnosis_id) as referral_count
                FROM diagnosis d
                JOIN kenya_specialist_hospitals h ON d.hospital_referral_id = h.hospital_id
                GROUP BY h.hospital_id
                ORDER BY referral_count DESC
            """)
            return cursor.fetchall()

# Helper function to get disease distribution by severity
def get_disease_by_severity():
    with get_db_connection() as conn:
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT severity_level, COUNT(*) as count
                FROM diseases
                GROUP BY severity_level
            """)
            return cursor.fetchall()

# Routes
@app.route("/")
def index():
    conn = get_db_connection()
    with conn.cursor() as cursor:
        cursor.execute("SELECT COUNT(*) as count FROM patient")
        patient_count = cursor.fetchone()['count']
        
        cursor.execute("SELECT COUNT(*) as count FROM diagnosis")
        diagnosis_count = cursor.fetchone()['count']
        
        cursor.execute("SELECT AVG(match_confidence) as avg FROM diagnosis")
        avg_confidence = cursor.fetchone()['avg']

    disease_by_age = get_disease_by_age()
    disease_by_religion = get_disease_by_religion()
    disease_by_hospital = get_disease_by_hospital()
    disease_by_referrals = get_disease_by_referrals()
    disease_by_severity = get_disease_by_severity()

    return render_template(
        "dashboard.html",
        patient_count=patient_count,
        diagnosis_count=diagnosis_count,
        avg_confidence=avg_confidence,
        disease_by_age=disease_by_age,
        disease_by_religion=disease_by_religion,
        disease_by_hospital=disease_by_hospital,
        disease_by_referrals=disease_by_referrals,
        disease_by_severity=disease_by_severity
    )

if __name__ == "__main__":
    app.run(debug=True)