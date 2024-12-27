from flask import Flask, render_template
from sqlalchemy import create_engine, func
from sqlalchemy.orm import sessionmaker
from models import Base, Patient, Disease
import plotly
import plotly.graph_objs as go
import json
import pandas as pd

app = Flask(__name__)

# Database Connection
engine = create_engine('mysql+pymysql://root:@localhost/medical')
Session = sessionmaker(bind=engine)

def get_disease_age_distribution():
    session = Session()
    
    
    df = pd.read_sql('''
        SELECT 
            d.disease_name, 
            CASE 
                WHEN p.age BETWEEN 0 AND 12 THEN '0-12'
                WHEN p.age BETWEEN 13 AND 19 THEN '13-19'
                WHEN p.age BETWEEN 20 AND 35 THEN '20-35'
                WHEN p.age BETWEEN 36 AND 50 THEN '36-50'
                ELSE '50+'
            END as age_group,
            COUNT(*) as disease_count
        FROM diseases d
        JOIN patients p ON d.pid = p.id
        GROUP BY d.disease_name, age_group
    ''', engine)
    
    # Create a grouped bar chart
    fig = go.Figure()
    for disease in df['disease_name'].unique():
        disease_data = df[df['disease_name'] == disease]
        fig.add_trace(go.Bar(
            x=disease_data['age_group'], 
            y=disease_data['disease_count'], 
            name=disease
        ))
    
    fig.update_layout(
        title='Disease Distribution Across Age Groups',
        xaxis_title='Age Group',
        yaxis_title='Number of Cases',
        barmode='group'
    )
    
    return json.dumps(fig, cls=plotly.utils.PlotlyJSONEncoder)

def get_disease_by_city():
    session = Session()
    
    
    df = pd.read_sql('''
        SELECT 
            d.disease_name, 
            p.currentcity,
            COUNT(*) as disease_count
        FROM diseases d
        JOIN patients p ON d.pid = p.id
        GROUP BY d.disease_name, p.currentcity
    ''', engine)
    
    # Create a bar chart
    fig = go.Figure()
    for disease in df['disease_name'].unique():
        disease_data = df[df['disease_name'] == disease]
        fig.add_trace(go.Bar(
            x=disease_data['currentcity'], 
            y=disease_data['disease_count'], 
            name=disease
        ))
    
    fig.update_layout(
        title='Disease Distribution by City',
        xaxis_title='City',
        yaxis_title='Number of Cases',
        barmode='group'
    )
    
    return json.dumps(fig, cls=plotly.utils.PlotlyJSONEncoder)

def get_disease_by_severity():
    session = Session()
    
    df = pd.read_sql('''
        SELECT 
            disease_name, 
            severity_level,
            COUNT(*) as disease_count
        FROM diseases
        GROUP BY disease_name, severity_level
    ''', engine)
    
    # Create a bar chart
    fig = go.Figure()
    for disease in df['disease_name'].unique():
        disease_data = df[df['disease_name'] == disease]
        fig.add_trace(go.Bar(
            x=disease_data['severity_level'], 
            y=disease_data['disease_count'], 
            name=disease
        ))
    
    fig.update_layout(
        title='Disease Distribution by Severity Level',
        xaxis_title='Severity Level',
        yaxis_title='Number of Cases',
        barmode='group'
    )
    
    return json.dumps(fig, cls=plotly.utils.PlotlyJSONEncoder)

def get_disease_by_gender():
    session = Session()
    
    df = pd.read_sql('''
        SELECT 
            d.disease_name, 
            p.gender,
            COUNT(*) as disease_count
        FROM diseases d
        JOIN patients p ON d.pid = p.id
        GROUP BY d.disease_name, p.gender
    ''', engine)
    
    # Create a bar chart
    fig = go.Figure()
    for disease in df['disease_name'].unique():
        disease_data = df[df['disease_name'] == disease]
        fig.add_trace(go.Bar(
            x=disease_data['gender'], 
            y=disease_data['disease_count'], 
            name=disease
        ))
    
    fig.update_layout(
        title='Disease Distribution by Gender',
        xaxis_title='Gender',
        yaxis_title='Number of Cases',
        barmode='group'
    )
    
    return json.dumps(fig, cls=plotly.utils.PlotlyJSONEncoder)

def get_top_recommended_hospitals():
    session = Session()
    
    df = pd.read_sql('''
        SELECT 
            hospital_recommended, 
            disease_name,
            COUNT(*) as recommendation_count
        FROM diseases
        WHERE hospital_recommended IS NOT NULL
        GROUP BY hospital_recommended, disease_name
        ORDER BY hospital_recommended, recommendation_count DESC
    ''', engine)
    
   
    fig = go.Figure()
    

    hospitals = df['hospital_recommended'].unique()
    diseases = df['disease_name'].unique()
    
    
    for hospital in hospitals:
        # Filter data for this hospital
        hospital_data = df[df['hospital_recommended'] == hospital]
        
        # Prepare x, y, and name values
        x_vals = [hospital] * len(hospital_data)
        y_vals = hospital_data['recommendation_count']
        names = hospital_data['disease_name'].tolist()
        
        # Add trace for this hospital
        fig.add_trace(go.Bar(
            x=x_vals, 
            y=y_vals,
            name=hospital,  # Use hospital name as the trace name
            text=names,  # Add disease names as text
            textposition='inside'
        ))
    
    fig.update_layout(
        title='Diseases Recommended for Each Hospital',
        xaxis_title='Hospital',
        yaxis_title='Number of Recommendations',
        barmode='stack'
    )
    
    return json.dumps(fig, cls=plotly.utils.PlotlyJSONEncoder)

@app.route('/')
def dashboard():
    return render_template('dashboard.html', 
        disease_age_chart=get_disease_age_distribution(),
        disease_city_chart=get_disease_by_city(),
        disease_severity_chart=get_disease_by_severity(),
        disease_gender_chart=get_disease_by_gender(),
        top_hospitals_chart=get_top_recommended_hospitals()
    )

if __name__ == '__main__':
    app.run(debug=True)