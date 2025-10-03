import React from 'react';
import { Link as RouterLink } from 'react-router-dom';
import {
  Box,
  Container,
  Typography,
  Button,
  Grid,
  Card,
  CardContent,
  CardMedia,
} from '@mui/material';
import {
  Restaurant as RestaurantIcon,
  Kitchen as KitchenIcon,
  CalendarToday as CalendarIcon,
  People as PeopleIcon,
} from '@mui/icons-material';

const features = [
  {
    title: 'Dining Out',
    description: 'Discover family-friendly restaurants with reviews from real families.',
    icon: <RestaurantIcon sx={{ fontSize: 48 }} />,
    color: 'primary.main',
  },
  {
    title: 'Dining In',
    description: 'AI-powered meal planning with recipes tailored to your family.',
    icon: <KitchenIcon sx={{ fontSize: 48 }} />,
    color: 'secondary.main',
  },
  {
    title: 'Meal Planning',
    description: 'Plan your week with smart suggestions and grocery integration.',
    icon: <CalendarIcon sx={{ fontSize: 48 }} />,
    color: 'info.main',
  },
  {
    title: 'Community',
    description: 'Connect with other families and share your experiences.',
    icon: <PeopleIcon sx={{ fontSize: 48 }} />,
    color: 'warning.main',
  },
];

function Landing() {
  return (
    <Box>
      {/* Hero Section */}
      <Box
        sx={{
          background: 'linear-gradient(135deg, #FF6B35 0%, #FF8A5B 100%)',
          color: 'white',
          py: 10,
        }}
      >
        <Container maxWidth="lg">
          <Grid container spacing={4} alignItems="center">
            <Grid item xs={12} md={6}>
              <Typography variant="h1" gutterBottom>
                Where Families Connect Over Food
              </Typography>
              <Typography variant="h5" paragraph>
                Simplify meal planning, discover family-friendly restaurants,
                and make every meal memorable.
              </Typography>
              <Box sx={{ mt: 4 }}>
                <Button
                  variant="contained"
                  size="large"
                  color="secondary"
                  component={RouterLink}
                  to="/signup"
                  sx={{ mr: 2 }}
                >
                  Get Started Free
                </Button>
                <Button
                  variant="outlined"
                  size="large"
                  sx={{ 
                    color: 'white', 
                    borderColor: 'white',
                    '&:hover': {
                      borderColor: 'white',
                      backgroundColor: 'rgba(255, 255, 255, 0.1)',
                    }
                  }}
                  component={RouterLink}
                  to="/login"
                >
                  Sign In
                </Button>
              </Box>
            </Grid>
            <Grid item xs={12} md={6}>
              <Box
                component="img"
                src="/images/hero-family-dining.jpg"
                alt="Family dining together"
                sx={{
                  width: '100%',
                  borderRadius: 2,
                  boxShadow: 3,
                }}
              />
            </Grid>
          </Grid>
        </Container>
      </Box>

      {/* Features Section */}
      <Container maxWidth="lg" sx={{ py: 10 }}>
        <Typography variant="h2" align="center" gutterBottom>
          Everything Your Family Needs
        </Typography>
        <Typography variant="h6" align="center" color="text.secondary" paragraph>
          From planning to dining, we've got you covered
        </Typography>
        
        <Grid container spacing={4} sx={{ mt: 4 }}>
          {features.map((feature, index) => (
            <Grid item xs={12} sm={6} md={3} key={index}>
              <Card
                sx={{
                  height: '100%',
                  textAlign: 'center',
                  transition: 'transform 0.2s',
                  '&:hover': {
                    transform: 'translateY(-8px)',
                    boxShadow: 4,
                  },
                }}
              >
                <CardContent>
                  <Box
                    sx={{
                      color: feature.color,
                      mb: 2,
                    }}
                  >
                    {feature.icon}
                  </Box>
                  <Typography variant="h5" gutterBottom>
                    {feature.title}
                  </Typography>
                  <Typography variant="body2" color="text.secondary">
                    {feature.description}
                  </Typography>
                </CardContent>
              </Card>
            </Grid>
          ))}
        </Grid>
      </Container>
    </Box>
  );
}

export default Landing;