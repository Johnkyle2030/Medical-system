from sqlalchemy import create_engine, Column, Integer, String, Date, Enum, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship, sessionmaker
import enum

Base = declarative_base()

class GenderEnum(enum.Enum):
    male = "male"
    female = "female"
    other = "other"

class SeverityEnum(enum.Enum):
    mild = "mild"
    moderate = "moderate"
    critical = "critical"

class Patient(Base):
    __tablename__ = 'patients'
    
    id = Column(Integer, primary_key=True)
    fname = Column(String(50), nullable=False)
    lname = Column(String(50), nullable=False)
    gender = Column(Enum(GenderEnum), nullable=False)
    email = Column(String(100), unique=True, nullable=False)
    contact = Column(String(20))
    password = Column(String(255), nullable=False)
    date_of_birth = Column(Date, nullable=False)
    birth_place = Column(String(100), nullable=False)
    currentcity = Column(String(100), nullable=False)
    age = Column(Integer, nullable=False)
    religion = Column(String(100))
    
    # Relationship with Diseases
    diseases = relationship("Disease", back_populates="patient")

class Disease(Base):
    __tablename__ = 'diseases'
    
    id = Column(Integer, primary_key=True)
    pid = Column(Integer, ForeignKey('patients.id'), nullable=False)
    disease_name = Column(String(255), nullable=False)
    hospital_recommended = Column(String(255))
    severity_level = Column(Enum(SeverityEnum), nullable=False)
    medication = Column(String)
    
    # Relationship with Patient
    patient = relationship("Patient", back_populates="diseases")