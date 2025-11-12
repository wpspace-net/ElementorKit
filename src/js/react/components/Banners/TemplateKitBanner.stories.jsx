import React from 'react'
import TemplateKitBanner from './TemplateKitBanner'
import GlobalConfigProvider from '../Contexts/GlobalConfigProvider'

export default { title: 'banners' }

export const templateKit = () => {
  return (
    <GlobalConfigProvider config={{
      api_url: 'https://api.elementorkit.site'
    }}
    >
      <TemplateKitBanner />
    </GlobalConfigProvider>
  )
}
